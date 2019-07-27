<?php

namespace Varbox\Tests\Integration\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Varbox\Models\Revision;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Author;
use Varbox\Tests\Models\Tag;
use Varbox\Tests\Models\Post;
use Varbox\Tests\Models\Comment;
use Varbox\Options\RevisionOptions;

class HasRevisionsTest extends TestCase
{
    /**
     * @var Post
     */
    protected $post;

    /**
     * @var Author
     */
    protected $author;

    /** @test */
    public function it_automatically_creates_a_revision_when_the_record_changes()
    {
        $this->createPost();
        $this->modifyPost();

        $this->assertEquals(1, Revision::count());
    }

    /** @test */
    public function it_can_manually_create_a_revision()
    {
        $this->createPost();

        $this->post->saveAsRevision();

        $this->assertEquals(1, Revision::count());
    }

    /** @test */
    public function it_stores_the_original_attribute_values_when_creating_a_revision()
    {
        $this->createPost();
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertEquals('Post name', $revision->data['name']);
        $this->assertEquals('post-slug', $revision->data['slug']);
        $this->assertEquals('Post content', $revision->data['content']);
        $this->assertEquals(10, $revision->data['votes']);
        $this->assertEquals(100, $revision->data['views']);
    }

    /** @test */
    public function it_can_rollback_to_a_past_revision()
    {
        $this->createPost();
        $this->modifyPost();

        $this->assertEquals('Another post name', $this->post->name);
        $this->assertEquals('another-post-slug', $this->post->slug);
        $this->assertEquals('Another post content', $this->post->content);
        $this->assertEquals(20, $this->post->votes);
        $this->assertEquals(200, $this->post->views);

        $this->post->rollbackToRevision($this->post->revisions()->first());

        $this->assertEquals('Post name', $this->post->name);
        $this->assertEquals('post-slug', $this->post->slug);
        $this->assertEquals('Post content', $this->post->content);
        $this->assertEquals(10, $this->post->votes);
        $this->assertEquals(100, $this->post->views);
    }

    /** @test */
    public function it_creates_a_new_revision_when_rolling_back_to_a_past_revision()
    {
        $this->createPost();
        $this->modifyPost();

        $this->post->rollbackToRevision($this->post->revisions()->first());

        $this->assertEquals(2, Revision::count());
    }

    /** @test */
    public function it_can_delete_all_revisions_of_a_record()
    {
        $this->createPost();
        $this->modifyPost();
        $this->modifyPostAgain();

        $this->assertEquals(2, Revision::count());

        $this->post->deleteAllRevisions();

        $this->assertEquals(0, Revision::count());
    }

    /** @test */
    public function it_can_create_a_revision_when_creating_the_record()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->enableRevisionOnCreate();
            }
        };

        $this->createPost($model);

        $this->assertEquals(1, Revision::count());
    }

    /** @test */
    public function it_can_limit_the_number_of_revisions_a_record_can_have()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->limitRevisionsTo(5);
            }
        };

        $this->createPost($model);

        for ($i = 1; $i <= 10; $i++) {
            $this->modifyPost();
            $this->modifyPostAgain();
        }

        $this->assertEquals(5, Revision::count());
    }

    /** @test */
    public function it_deletes_the_oldest_revisions_when_the_limit_is_achieved()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->limitRevisionsTo(5);
            }
        };

        $this->createPost($model);

        for ($i = 1; $i <= 10; $i++) {
            $this->modifyPost();
            $this->modifyPostAgain();
        }

        $this->assertEquals(16, $this->post->revisions()->oldest()->first()->id);
    }

    /** @test */
    public function it_can_specify_only_certain_fields_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->fieldsToRevision('name', 'votes');
            }
        };

        $this->createPost($model);
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayHasKey('name', $revision->data);
        $this->assertArrayHasKey('votes', $revision->data);
        $this->assertArrayNotHasKey('slug', $revision->data);
        $this->assertArrayNotHasKey('content', $revision->data);
        $this->assertArrayNotHasKey('views', $revision->data);
    }

    /** @test */
    public function it_can_exclude_certain_fields_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->fieldsToNotRevision('name', 'votes');
            }
        };

        $this->createPost($model);
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayNotHasKey('name', $revision->data);
        $this->assertArrayNotHasKey('votes', $revision->data);
        $this->assertArrayHasKey('slug', $revision->data);
        $this->assertArrayHasKey('content', $revision->data);
        $this->assertArrayHasKey('views', $revision->data);
    }

    /** @test */
    public function it_can_include_timestamps_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->withTimestamps();
            }
        };

        $this->createPost($model);
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayHasKey('created_at', $revision->data);
        $this->assertArrayHasKey('updated_at', $revision->data);
    }

    /** @test */
    public function it_can_save_belongs_to_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('author');
            }
        };

        $this->createAuthor();
        $this->createPost($model);
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayHasKey('author', $revision->data['relations']);
        $this->assertArrayHasKey('records', $revision->data['relations']['author']);
        $this->assertEquals(BelongsTo::class, $revision->data['relations']['author']['type']);

        $this->assertEquals($this->post->author->title, $revision->data['relations']['author']['records']['items'][0]['title']);
        $this->assertEquals($this->post->author->name, $revision->data['relations']['author']['records']['items'][0]['name']);
        $this->assertEquals($this->post->author->age, $revision->data['relations']['author']['records']['items'][0]['age']);
    }

    /** @test */
    public function it_stores_the_original_attribute_values_of_belongs_to_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('author');
            }
        };

        $this->createAuthor();
        $this->createPost($model);
        $this->modifyPost();

        $this->post->author()->update([
            'title' => 'Author title updated',
            'name' => 'Author name updated',
            'age' => 100,
        ]);

        $author = $this->post->author;
        $revision = $this->post->revisions()->first();

        $this->assertEquals('Author title updated', $author->title);
        $this->assertEquals('Author name updated', $author->name);
        $this->assertEquals('100', $author->age);

        $this->assertEquals('Author title', $revision->data['relations']['author']['records']['items'][0]['title']);
        $this->assertEquals('Author name', $revision->data['relations']['author']['records']['items'][0]['name']);
        $this->assertEquals('30', $revision->data['relations']['author']['records']['items'][0]['age']);
    }

    /** @test */
    public function it_rolls_back_belongs_to_relations_when_rolling_back_to_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('author');
            }
        };

        $this->createAuthor();
        $this->createPost($model);
        $this->modifyPost();

        $this->post->author()->update([
            'title' => 'Author title updated',
            'name' => 'Author name updated',
            'age' => 100,
        ]);

        $this->post->rollbackToRevision(
            $this->post->revisions()->first()
        );

        $author = $this->post->fresh()->author;

        $this->assertEquals('Author title', $author->title);
        $this->assertEquals('Author name', $author->name);
        $this->assertEquals('30', $author->age);
    }

    /** @test */
    public function it_can_save_has_one_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('review');
            }
        };

        $this->createPost($model);
        $this->createReview();
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayHasKey('review', $revision->data['relations']);
        $this->assertArrayHasKey('records', $revision->data['relations']['review']);
        $this->assertEquals(HasOne::class, $revision->data['relations']['review']['type']);

        $this->assertEquals($this->post->id, $revision->data['relations']['review']['records']['items'][0]['post_id']);
        $this->assertEquals('Review name', $revision->data['relations']['review']['records']['items'][0]['name']);
        $this->assertEquals('Review content', $revision->data['relations']['review']['records']['items'][0]['content']);
    }

    /** @test */
    public function it_stores_the_original_attribute_values_of_has_one_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('review');
            }
        };

        $this->createPost($model);
        $this->createReview();
        $this->modifyPost();

        $this->post->review()->update([
            'name' => 'Review name updated',
            'content' => 'Review content updated',
        ]);

        $review = $this->post->review;
        $revision = $this->post->revisions()->first();

        $this->assertEquals('Review name updated', $review->name);
        $this->assertEquals('Review content updated', $review->content);

        $this->assertEquals('Review name', $revision->data['relations']['review']['records']['items'][0]['name']);
        $this->assertEquals('Review content', $revision->data['relations']['review']['records']['items'][0]['content']);
    }

    /** @test */
    public function it_rolls_back_has_one_relations_when_rolling_back_to_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('review');
            }
        };

        $this->createPost($model);
        $this->createReview();
        $this->modifyPost();

        $this->post->review()->update([
            'name' => 'Review name updated',
            'content' => 'Review content updated',
        ]);

        $this->post->rollbackToRevision(
            $this->post->revisions()->first()
        );

        $review = $this->post->fresh()->review;

        $this->assertEquals('Review name', $review->name);
        $this->assertEquals('Review content', $review->content);
    }

    /** @test */
    public function it_removes_extra_created_has_one_relations_when_rolling_back_to_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('review');
            }
        };

        $this->createPost($model);
        $this->createReview();
        $this->modifyPost();

        $relatedCountForRevisionCheckpoint = $this->post->review()->count();

        $this->post->review()->create([
            'name' => 'Extra Review name',
            'content' => 'Extra Review content',
        ]);

        $this->post->rollbackToRevision(
            $this->post->revisions()->first()
        );

        $this->assertEquals($relatedCountForRevisionCheckpoint, $this->post->review()->count());
    }

    /** @test */
    public function it_can_save_has_many_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('comments');
            }
        };

        $this->createPost($model);
        $this->createComments();
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayHasKey('comments', $revision->data['relations']);
        $this->assertArrayHasKey('records', $revision->data['relations']['comments']);
        $this->assertEquals(HasMany::class, $revision->data['relations']['comments']['type']);

        for ($i = 1; $i <= 3; $i++) {
            $comment = Comment::limit(1)->offset($i - 1)->first();

            $this->assertEquals($this->post->id, $revision->data['relations']['comments']['records']['items'][$i - 1]['post_id']);
            $this->assertEquals($comment->title, $revision->data['relations']['comments']['records']['items'][$i - 1]['title']);
            $this->assertEquals($comment->content, $revision->data['relations']['comments']['records']['items'][$i - 1]['content']);
            $this->assertEquals($comment->date, $revision->data['relations']['comments']['records']['items'][$i - 1]['date']);
            $this->assertEquals($comment->active, $revision->data['relations']['comments']['records']['items'][$i - 1]['active']);
        }
    }

    /** @test */
    public function it_stores_the_original_attribute_values_of_has_many_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('comments');
            }
        };

        $this->createPost($model);
        $this->createComments();
        $this->modifyPost();

        for ($i = 1; $i <= 3; $i++) {
            $this->post->comments()->limit(1)->offset($i - 1)->first()->update([
                'title' => 'Comment title '.$i.' updated',
                'content' => 'Comment content '.$i.' updated',
                'active' => false,
            ]);
        }

        $revision = $this->post->revisions()->first();

        for ($i = 1; $i <= 3; $i++) {
            $comment = $this->post->fresh()->comments()->limit(1)->offset($i - 1)->first();

            $this->assertEquals('Comment title '.$i.' updated', $comment->title);
            $this->assertEquals('Comment content '.$i.' updated', $comment->content);
            $this->assertEquals(0, $comment->active);

            $this->assertEquals('Comment title '.$i, $revision->data['relations']['comments']['records']['items'][$i - 1]['title']);
            $this->assertEquals('Comment content '.$i, $revision->data['relations']['comments']['records']['items'][$i - 1]['content']);
            $this->assertEquals(1, $revision->data['relations']['comments']['records']['items'][$i - 1]['active']);
        }
    }

    /** @test */
    public function it_rolls_back_has_many_relations_when_rolling_back_to_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('comments');
            }
        };

        $this->createPost($model);
        $this->createComments();
        $this->modifyPost();

        for ($i = 1; $i <= 3; $i++) {
            $this->post->comments()->limit(1)->offset($i - 1)->first()->update([
                'title' => 'Comment title '.$i.' updated',
                'content' => 'Comment content '.$i.' updated',
                'active' => false,
            ]);
        }

        $this->post->rollbackToRevision(
            $this->post->revisions()->first()
        );

        for ($i = 1; $i <= 3; $i++) {
            $comment = $this->post->fresh()->comments()->limit(1)->offset($i - 1)->first();

            $this->assertEquals('Comment title '.$i, $comment->title);
            $this->assertEquals('Comment content '.$i, $comment->content);
            $this->assertEquals(1, $comment->active);
        }
    }

    /** @test */
    public function it_removes_extra_created_has_many_relations_when_rolling_back_to_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('comments');
            }
        };

        $this->createPost($model);
        $this->createComments();
        $this->modifyPost();

        $relatedCountForRevisionCheckpoint = $this->post->comments()->count();

        $this->post->comments()->create([
            'title' => 'Extra comment title',
            'content' => 'Extra comment content',
            'date' => Carbon::now(),
            'active' => true,
        ]);

        $this->post->rollbackToRevision(
            $this->post->revisions()->first()
        );

        $this->assertEquals($relatedCountForRevisionCheckpoint, $this->post->comments()->count());
    }

    /** @test */
    public function it_can_save_belongs_to_many_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('tags');
            }
        };

        $this->createPost($model);
        $this->createTags();
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->assertArrayHasKey('tags', $revision->data['relations']);
        $this->assertArrayHasKey('records', $revision->data['relations']['tags']);
        $this->assertArrayHasKey('pivots', $revision->data['relations']['tags']);
        $this->assertEquals(BelongsToMany::class, $revision->data['relations']['tags']['type']);

        for ($i = 1; $i <= 3; $i++) {
            $tag = Tag::find($i);

            $this->assertEquals($tag->name, $revision->data['relations']['tags']['records']['items'][$i - 1]['name']);
            $this->assertEquals($this->post->id, $revision->data['relations']['tags']['pivots']['items'][$i - 1]['post_id']);
            $this->assertEquals($tag->id, $revision->data['relations']['tags']['pivots']['items'][$i - 1]['tag_id']);
        }
    }

    /** @test */
    public function it_stores_the_original_pivot_values_of_belongs_to_many_relations_when_creating_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('tags');
            }
        };

        $this->createPost($model);
        $this->createTags();
        $this->modifyPost();

        $revision = $this->post->revisions()->first();

        $this->post->tags()->detach(
            $this->post->tags()->first()->id
        );

        $this->assertEquals(3, count($revision->data['relations']['tags']['pivots']['items']));
    }

    /** @test */
    public function it_rolls_back_belongs_to_many_relations_when_rolling_back_to_a_revision()
    {
        $model = new class extends Post {
            public function getRevisionOptions() : RevisionOptions
            {
                return parent::getRevisionOptions()->relationsToRevision('tags');
            }
        };

        $this->createPost($model);
        $this->createTags();
        $this->modifyPost();

        $this->post->tags()->detach(
            $this->post->tags()->first()->id
        );

        $this->assertEquals(2, $this->post->tags()->count());

        $this->post->rollbackToRevision(
            $this->post->revisions()->first()
        );

        $this->assertEquals(3, $this->post->tags()->count());
    }

    /**
     * @param Post|null $model
     * @return void
     */
    protected function createPost(Post $model = null)
    {
        $model = $model && $model instanceof Post ? $model : new Post;

        $this->post = $model->create([
            'author_id' => $this->author ? $this->author->id : null,
            'name' => 'Post name',
            'slug' => 'post-slug',
            'content' => 'Post content',
            'votes' => 10,
            'views' => 100,
        ]);

        $this->post = $this->post->fresh();
    }

    /**
     * @return void
     */
    protected function createAuthor()
    {
        $this->author = Author::create([
            'title' => 'Author title',
            'name' => 'Author name',
            'age' => 30,
        ]);
    }

    /**
     * @return void
     */
    protected function createReview()
    {
        $this->post->review()->create([
            'post_id' => $this->post->id,
            'name' => 'Review name',
            'content' => 'Review content',
        ]);
    }

    /**
     * @return void
     */
    protected function createComments()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->post->comments()->create([
                'id' => $i,
                'title' => 'Comment title '.$i,
                'content' => 'Comment content '.$i,
                'date' => Carbon::now(),
                'active' => true,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function createTags()
    {
        for ($i = 1; $i <= 3; $i++) {
            Tag::create([
                'name' => 'Tag name '.$i,
            ]);
        }

        $this->post->tags()->attach(Tag::pluck('id')->toArray());
    }

    /**
     * @return void
     */
    protected function modifyPost()
    {
        $this->post->update([
            'name' => 'Another post name',
            'slug' => 'another-post-slug',
            'content' => 'Another post content',
            'votes' => 20,
            'views' => 200,
        ]);
    }

    /**
     * @return void
     */
    protected function modifyPostAgain()
    {
        $this->post->update([
            'name' => 'Yet another post name',
            'slug' => 'yet-another-post-slug',
            'content' => 'Yet another post content',
            'votes' => 30,
            'views' => 300,
        ]);
    }
}
