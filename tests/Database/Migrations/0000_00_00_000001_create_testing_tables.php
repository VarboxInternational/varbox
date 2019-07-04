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
        Schema::create('test_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamps();
        });

        Schema::create('test_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('content')->nullable();
            $table->integer('votes')->default(0);
            $table->integer('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade')->onUpdate('set null');
            $table->timestamps();
        });

        Schema::create('test_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->string('name');
            $table->text('content')->nullable();
            $table->integer('rating')->default(0);
            $table->timestamps();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('test_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->string('title');
            $table->text('content');
            $table->date('date')->nullable();
            $table->boolean('active')->default(false);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('test_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('test_post_tag', function (Blueprint $table) {
            $table->integer('post_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->timestamps();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->primary(['post_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_post_tag');
        Schema::dropIfExists('test_tags');
        Schema::dropIfExists('test_comments');
        Schema::dropIfExists('test_reviews');
        Schema::dropIfExists('test_posts');
        Schema::dropIfExists('test_authors');
    }
}
