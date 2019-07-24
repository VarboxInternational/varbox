<?php

namespace Varbox\Tests\Integration\Traits;

use Varbox\Options\DuplicateOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Tag;
use Varbox\Tests\Models\Post;
use Varbox\Tests\Models\Review;
use Varbox\Tests\Models\Comment;

class HasDuplicatesTest extends TestCase
{
    /**
     * @var Post
     */
    protected $post;

    /**
     * @var Review
     */
    protected $review;

    /**
     * @var array
     */
    protected $comments = [];

    /**
     * @var array
     */
    protected $tags = [];

    /** @test */
    public function it_duplicates_a_model_instance()
    {
        $this->createPost();

        $model = $this->post->saveAsDuplicate();

        foreach ($this->post->getFillable() as $field) {
            $this->assertEquals($this->post->{$field}, $model->{$field});
        }

        $this->assertEquals(2, Post::count());
    }

    /** @test */
    public function it_can_save_unique_columns_when_duplicating_a_model_instance()
    {
        $model = new class extends Post {
            public function getDuplicateOptions() : DuplicateOptions
            {
                return parent::getDuplicateOptions()->uniqueColumns('name');
            }
        };

        $this->createPost($model);

        for ($i = 1; $i <= 5; $i++) {
            $model = $this->post->saveAsDuplicate();

            $this->assertEquals($this->post->name.' ('.$i.')', $model->name);
        }
    }

    /** @test */
    public function it_can_exclude_columns_when_duplicating_a_model_instance()
    {
        $model = new class extends Post {
            public function getDuplicateOptions() : DuplicateOptions
            {
                return parent::getDuplicateOptions()->excludeColumns('views', 'approved', 'published_at');
            }
        };

        $this->createPost($model);

        for ($i = 1; $i <= 5; $i++) {
            $model = $this->post->saveAsDuplicate();
            $model = $model->fresh();

            $this->assertEquals(0, $model->views);
            $this->assertEquals(0, $model->approved);
            $this->assertNull($model->published_at);
        }
    }

    /** @test */
    public function it_can_duplicate_one_to_one_relations_when_duplicating_a_model_instance()
    {
        $this->createPost();
        $this->createReview();

        $model = $this->post->saveAsDuplicate();
        $fields = array_filter($this->review->getFillable(), function ($value) {
            return $value != 'post_id';
        });

        foreach ($fields as $field) {
            $this->assertEquals($this->review->{$field}, $model->review->{$field});
        }

        $this->assertEquals(2, Review::count());
    }

    /** @test */
    public function it_can_duplicate_one_to_many_relations_when_duplicating_a_model_instance()
    {
        $this->createPost();
        $this->createComments();

        $model = $this->post->saveAsDuplicate();

        foreach ($model->comments as $index => $comment) {
            $fields = array_filter($comment->getFillable(), function ($value) {
                return $value != 'post_id';
            });

            foreach ($fields as $field) {
                $this->assertEquals($this->comments[$index]->{$field}, $comment->{$field});
            }
        }

        $this->assertEquals(6, Comment::count());
    }

    /** @test */
    public function it_can_duplicate_many_to_many_relations_when_duplicating_a_model_instance()
    {
        $this->createPost();
        $this->createTags();

        $this->assertEquals(3, Tag::count());

        $model = $this->post->saveAsDuplicate();

        foreach ($model->tags as $index => $tag) {
            $this->assertEquals($model->id, $tag->pivot->post_id);
            $this->assertEquals($this->tags[$index]->id, $tag->pivot->tag_id);
        }

        $this->assertEquals(3, Tag::count());
    }

    /** @test */
    public function it_can_save_unique_columns_when_duplicating_a_relation_of_the_model_instance()
    {
        $model = new class extends Post {
            public function getDuplicateOptions() : DuplicateOptions
            {
                return parent::getDuplicateOptions()->uniqueRelationColumns([
                    'review' => ['name'], 'comments' => ['content'],
                ]);
            }
        };

        $this->createPost($model);
        $this->createReview();
        $this->createComments();

        $count = 1;

        for ($i = 1; $i <= 5; $i++) {
            $model = $this->post->saveAsDuplicate();
            $model = $model->fresh();

            $this->assertEquals($this->post->review->name.' ('.$i.')', $model->review->name);

            foreach ($model->comments as $index => $comment) {
                $this->assertEquals($this->comments[$index]->content.' ('.$count.')', $comment->content);

                $count++;
            }
        }
    }

    /** @test */
    public function it_can_exclude_columns_when_duplicating_a_relation_of_the_model_instance()
    {
        $model = new class extends Post {
            public function getDuplicateOptions() : DuplicateOptions
            {
                return parent::getDuplicateOptions()->excludeRelationColumns([
                    'review' => ['content', 'rating'], 'comments' => ['content', 'votes'],
                ]);
            }
        };

        $this->createPost($model);
        $this->createReview();
        $this->createComments();

        for ($i = 1; $i <= 5; $i++) {
            $model = $this->post->saveAsDuplicate();
            $model = $model->fresh();

            $this->assertNull($model->review->content);
            $this->assertEquals(0, $model->review->rating);

            foreach ($model->comments as $index => $comment) {
                $this->assertNull($comment->comment);
                $this->assertEquals(0, $comment->votes);
            }
        }
    }

    /** @test */
    public function it_can_duplicate_only_the_targeted_model_instance_without_any_relations()
    {
        $model = new class extends Post {
            public function getDuplicateOptions() : DuplicateOptions
            {
                return parent::getDuplicateOptions()->disableDeepDuplication();
            }
        };

        $this->createPost($model);

        $model = $this->post->saveAsDuplicate();

        $this->assertEquals(0, $model->review()->count());
        $this->assertEquals(0, $model->comments()->count());
        $this->assertEquals(0, $model->tags()->count());
    }

    /**
     * @param Post|null $model
     * @return void
     */
    protected function createPost(Post $model = null)
    {
        $model = $model && $model instanceof Post ? $model : new Post;

        $this->post = $model->create([
            'name' => 'Post test name',
            'content' => 'Post test content',
            'views' => 100,
            'approved' => true,
            'published_at' => today(),
        ]);
    }

    /**
     * @return void
     */
    protected function createReview()
    {
        $this->review = $this->post->review()->create([
            'name' => 'Review test name',
            'content' => 'Review test content',
            'rating' => 5,
        ]);
    }

    /**
     * @return void
     */
    protected function createComments()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->comments[] = $this->post->comments()->create([
                'title' => 'Comment test subject ' . $i,
                'content' => 'Comment test comment',
                'date' => today()->subDays($i),
                'votes' => $i * 10,
                'active' => $i % 2 == 0,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function createTags()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->tags[] = Tag::create([
                'name' => 'Tag test name '.$i,
            ]);
        }

        foreach ($this->tags as $tag) {
            $this->post->tags()->attach($tag->id);
        }
    }
}
