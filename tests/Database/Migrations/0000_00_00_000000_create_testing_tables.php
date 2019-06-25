<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'name')) {
                    $table->dropColumn('name');
                }
            });

            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'first_name')) {
                    $table->string('first_name')->nullable()->after('password');
                }

                if (!Schema::hasColumn('users', 'last_name')) {
                    $table->string('last_name')->nullable()->after('first_name');
                }

                if (!Schema::hasColumn('users', 'active')) {
                    $table->boolean('active')->default(false)->after('last_name');
                }
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->unique();
                $table->string('guard');

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->unique();
                $table->string('guard');
                $table->string('group')->nullable();
                $table->string('label')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_role')) {
            Schema::create('user_role', function (Blueprint $table) {
                $table->bigInteger('user_id')->unsigned();
                $table->integer('role_id')->unsigned();

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

                $table->primary(['user_id', 'role_id']);
            });
        }

        if (!Schema::hasTable('user_permission')) {
            Schema::create('user_permission', function (Blueprint $table) {
                $table->bigInteger('user_id')->unsigned();
                $table->integer('permission_id')->unsigned();

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

                $table->primary(['user_id', 'permission_id']);
            });
        }

        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->integer('role_id')->unsigned();
                $table->integer('permission_id')->unsigned();

                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

                $table->primary(['role_id', 'permission_id']);
            });
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('type');
                $table->morphs('notifiable');

                $table->text('data');

                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('activity')) {
            Schema::create('activity', function (Blueprint $table) {
                $table->increments('id');

                $table->bigInteger('user_id')->unsigned()->index()->nullable();
                $table->nullableMorphs('subject');

                $table->string('entity_type')->nullable();
                $table->string('entity_name')->nullable();
                $table->string('entity_url')->nullable();

                $table->string('event');

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'active')) {
                    $table->dropColumn('active');
                }
            });

            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'last_name')) {
                    $table->dropColumn('last_name');
                }
            });

            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'first_name')) {
                    $table->dropColumn('first_name');
                }
            });

            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'name')) {
                    $table->string('name')->nullable()->after('id');
                }
            });
        }
    }
}
