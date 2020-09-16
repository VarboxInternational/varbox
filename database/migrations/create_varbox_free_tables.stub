<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVarboxFreeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'active')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('active')->default(false)->after('remember_token');
                });
            }
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

        if (!Schema::hasTable('uploads')) {
            Schema::create('uploads', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name');
                $table->string('original_name');
                $table->string('path');
                $table->string('full_path')->index()->unique();
                $table->string('extension');
                $table->integer('size')->default(0);
                $table->string('mime')->nullable();
                $table->enum('type', ['image', 'video', 'audio', 'file']);

                $table->timestamps();
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
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'active')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('active');
                });
            }
        }
    }
}
