<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreateVarboxPaidTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('urls')) {
            Schema::create('urls', function (Blueprint $table) {
                $table->increments('id');

                $table->string('url')->unique();
                $table->morphs('urlable');

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('revisions')) {
            Schema::create('revisions', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('user_id')->unsigned()->index()->nullable();

                $table->morphs('revisionable');
                $table->json('data')->nullable();

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
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
                $table->boolean('obsolete')->default(false);

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            });
        }

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->unique();
                $table->string('code')->unique();
                $table->string('flag')->nullable();
                $table->string('capital')->nullable();

                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('country_id')->unsigned()->index();

                $table->string('name');
                $table->string('code');

                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('country_id')->unsigned()->index();
                $table->integer('state_id')->unsigned()->index()->nullable();

                $table->string('name');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();

                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('set null');
            });
        }

        if (!Schema::hasTable('addresses') && Schema::hasTable('users')) {
            Schema::create('addresses', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('user_id')->unsigned()->index();
                $table->integer('country_id')->unsigned()->index()->nullable();
                $table->integer('state_id')->unsigned()->index()->nullable();
                $table->integer('city_id')->unsigned()->index()->nullable();

                $table->text('address')->nullable();
                $table->integer('ord')->default(0);

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
                $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('configs')) {
            Schema::create('configs', function (Blueprint $table) {
                $table->increments('id');

                $table->string('key')->unique();
                $table->string('value')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('errors')) {
            Schema::create('errors', function (Blueprint $table) {
                $table->increments('id');

                $table->string('type');
                $table->string('code')->nullable();
                $table->text('url')->nullable();
                $table->text('message')->nullable();
                $table->integer('occurrences')->default(1);
                $table->text('file')->nullable();
                $table->integer('line')->nullable();
                $table->longText('trace')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('backups')) {
            Schema::create('backups', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name');
                $table->string('disk');
                $table->string('path');
                $table->timestamp('date');
                $table->integer('size')->default(0);

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->increments('id');
                NestedSet::columns($table);

                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->string('type');

                $table->json('data')->nullable();
                $table->json('meta')->nullable();

                $table->timestamp('drafted_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->increments('id');
                $table->nullableMorphs('menuable');

                NestedSet::columns($table);

                $table->string('type')->nullable();
                $table->string('location')->nullable();

                $table->string('name');
                $table->string('url')->nullable();
                $table->string('route')->nullable();

                $table->json('data')->nullable();
                $table->boolean('active')->default(true);

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('emails')) {
            Schema::create('emails', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->unique();
                $table->string('type')->nullable();
                $table->json('data')->nullable();

                $table->timestamp('drafted_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blocks')) {
            Schema::create('blocks', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->unique();
                $table->string('type')->nullable();
                $table->json('data')->nullable();

                $table->timestamp('drafted_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blockables')) {
            Schema::create('blockables', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('block_id')->unsigned()->index();
                $table->morphs('blockable');
                $table->string('location')->nullable();
                $table->integer('ord')->default(0);

                $table->timestamps();

                $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade')->onUpdate('cascade');
            });
        }

        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->unique();
                $table->string('code')->unique();
                $table->boolean('default')->default(false);
                $table->boolean('active')->default(false);

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('translations')) {
            Schema::create('translations', function(Blueprint $table) {
                $table->increments('id');

                $table->string('key');
                $table->text('value')->nullable();
                $table->string('locale');
                $table->string('group');

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('redirects')) {
            Schema::create('redirects', function (Blueprint $table) {
                $table->increments('id');

                $table->string('old_url')->unique();
                $table->string('new_url');
                $table->smallInteger('status')->default(301);

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
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('blockables');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('emails');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('backups');
        Schema::dropIfExists('errors');
        Schema::dropIfExists('configs');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('activity');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('revisions');
        Schema::dropIfExists('urls');
    }
}
