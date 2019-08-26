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
        Schema::create('activity_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('cache_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('content')->nullable();
            $table->timestamps();
        });

        Schema::create('cache_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('cache_posts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamps();
        });

        Schema::create('duplicate_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('content')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('approved')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('duplicate_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('duplicate_posts')->onDelete('cascade');
            $table->string('name');
            $table->text('content')->nullable();
            $table->integer('rating')->default(0);
            $table->timestamps();
        });

        Schema::create('duplicate_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('duplicate_posts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->date('date')->nullable();
            $table->integer('votes')->default(0);
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('duplicate_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('duplicate_post_tag', function (Blueprint $table) {
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('duplicate_posts')->onDelete('cascade');
            $table->integer('tag_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('duplicate_tags')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('draft_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('content')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('approved')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('drafted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('filter_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('filter_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->foreign('author_id')->references('id')->on('filter_authors')->onDelete('cascade')->onUpdate('set null');
            $table->string('name');
            $table->integer('votes')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

        });

        Schema::create('filter_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('fitler_posts')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('filter_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('filter_posts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('revision_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamps();
        });

        Schema::create('revision_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->foreign('author_id')->references('id')->on('revision_authors')->onDelete('cascade')->onUpdate('set null');
            $table->string('name');
            $table->text('content')->nullable();
            $table->integer('votes')->default(0);
            $table->integer('views')->default(0);
            $table->timestamps();
        });

        Schema::create('revision_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('revision_posts')->onDelete('cascade');
            $table->string('name');
            $table->text('content')->nullable();
            $table->timestamps();
        });

        Schema::create('revision_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('revision_posts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->date('date')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('revision_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('revision_post_tag', function (Blueprint $table) {
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('revision_posts')->onDelete('cascade');
            $table->integer('tag_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('revision_tags')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('slug_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('sort_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamps();
        });

        Schema::create('sort_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->foreign('author_id')->references('id')->on('sort_authors')->onDelete('cascade')->onUpdate('set null');
            $table->string('name');
            $table->integer('views')->default(0);
            $table->timestamps();
        });

        Schema::create('sort_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('sort_posts')->onDelete('cascade');
            $table->string('name');
            $table->integer('rating')->default(0);
            $table->timestamps();
        });

        Schema::create('upload_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            \Varbox\Models\Upload::column('image', $table);
            \Varbox\Models\Upload::column('video', $table);
            \Varbox\Models\Upload::column('audio', $table);
            \Varbox\Models\Upload::column('file', $table);
            $table->timestamps();
        });

        Schema::create('url_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('test_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('name');
            $table->integer('age')->default(0);
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('test_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index()->nullable();
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade')->onUpdate('set null');
            $table->string('name');
            $table->text('content')->nullable();
            $table->integer('votes')->default(0);
            $table->integer('views')->default(0);
            $table->boolean('approved')->default(false);
            $table->timestamp('published_at')->nullable();
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
            $table->text('content')->nullable();
            $table->date('date')->nullable();
            $table->boolean('active')->default(false);
            $table->integer('votes')->default(0);
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
        Schema::dropIfExists('url_posts');
        Schema::dropIfExists('upload_posts');
        Schema::dropIfExists('sort_reviews');
        Schema::dropIfExists('sort_posts');
        Schema::dropIfExists('sort_authors');
        Schema::dropIfExists('slug_posts');
        Schema::dropIfExists('revision_post_tag');
        Schema::dropIfExists('revision_tags');
        Schema::dropIfExists('revision_comments');
        Schema::dropIfExists('revision_reviews');
        Schema::dropIfExists('revision_posts');
        Schema::dropIfExists('revision_authors');
        Schema::dropIfExists('filter_comments');
        Schema::dropIfExists('filter_reviews');
        Schema::dropIfExists('filter_posts');
        Schema::dropIfExists('filter_authors');
        Schema::dropIfExists('draft_posts');
        Schema::dropIfExists('duplicate_post_tag');
        Schema::dropIfExists('duplicate_tags');
        Schema::dropIfExists('duplicate_comments');
        Schema::dropIfExists('duplicate_reviews');
        Schema::dropIfExists('cache_comments');
        Schema::dropIfExists('cache_posts');
        Schema::dropIfExists('activity_posts');
    }
}
