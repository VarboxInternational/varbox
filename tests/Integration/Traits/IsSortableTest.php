<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Sorts\Sort;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\SortAuthor;
use Varbox\Tests\Models\SortPost;
use Varbox\Tests\Models\SortReview;

class IsSortableTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpTestingConditions();
    }

    /** @test */
    public function it_sorts_model_records_in_ascending_order()
    {
        $posts = SortPost::sorted([
            'sort' => 'name',
            'direction' => 'asc',
        ])->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name z', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'views',
            'direction' => 'asc',
        ])->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name y', $posts->last()->name);
    }

    /** @test */
    public function it_sorts_model_records_in_descending_order()
    {
        $posts = SortPost::sorted([
            'sort' => 'name',
            'direction' => 'desc',
        ])->get();

        $this->assertEquals('Post name z', $posts->first()->name);
        $this->assertEquals('Post name a', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'views',
            'direction' => 'desc',
        ])->get();

        $this->assertEquals('Post name y', $posts->first()->name);
        $this->assertEquals('Post name a', $posts->last()->name);
    }

    /** @test */
    public function it_sorts_model_records_in_ascending_order_by_a_belongs_to_relation()
    {
        $posts = SortPost::sorted([
            'sort' => 'author.name',
            'direction' => 'asc',
        ])->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name y', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'author.age',
            'direction' => 'asc',
        ])->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name y', $posts->last()->name);
    }

    /** @test */
    public function it_sorts_model_records_in_descending_order_by_a_belongs_to_relation()
    {
        $posts = SortPost::sorted([
            'sort' => 'author.name',
            'direction' => 'desc',
        ])->get();

        $this->assertEquals('Post name b', $posts->first()->name);
        $this->assertEquals('Post name z', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'author.age',
            'direction' => 'desc',
        ])->get();

        $this->assertEquals('Post name b', $posts->first()->name);
        $this->assertEquals('Post name z', $posts->last()->name);
    }

    /** @test */
    public function it_sorts_model_records_in_ascending_order_by_a_has_one_relation()
    {
        $posts = SortPost::sorted([
            'sort' => 'review.name',
            'direction' => 'asc',
        ])->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name y', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'review.rating',
            'direction' => 'asc',
        ])->get();

        $this->assertEquals('Post name y', $posts->first()->name);
        $this->assertEquals('Post name a', $posts->last()->name);
    }

    /** @test */
    public function it_sorts_model_records_in_descending_order_by_a_has_one_relation()
    {
        $posts = SortPost::sorted([
            'sort' => 'review.name',
            'direction' => 'desc',
        ])->get();

        $this->assertEquals('Post name y', $posts->first()->name);
        $this->assertEquals('Post name a', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'review.rating',
            'direction' => 'desc',
        ])->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name y', $posts->last()->name);
    }

    /** @test */
    public function it_supports_changing_the_default_sort_and_direction_parameters()
    {
        $sort = new class extends Sort {
            public function field()
            {
                return 'custom-sort';
            }

            public function direction()
            {
                return 'custom-direction';
            }
        };

        $posts = SortPost::sorted([
            'custom-sort' => 'name',
            'custom-direction' => 'asc',
        ], $sort)->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name z', $posts->last()->name);

        $posts = SortPost::sorted([
            'sort' => 'views',
            'direction' => 'asc',
        ], $sort)->get();

        $this->assertEquals('Post name a', $posts->first()->name);
        $this->assertEquals('Post name y', $posts->last()->name);
    }

    /**
     * @return void
     */
    protected function setupTestingConditions()
    {
        $author1 = SortAuthor::create([
            'name' => 'Author name a',
            'age' => 10,
        ]);

        $author2 = SortAuthor::create([
            'name' => 'Author name z',
            'age' => 20,
        ]);

        $post1 = SortPost::create([
            'author_id' => $author1->id,
            'name' => 'Post name a',
            'views' => 10,
        ]);

        $post2 = SortPost::create([
            'author_id' => $author1->id,
            'name' => 'Post name z',
            'views' => 20,
        ]);

        $post3 = SortPost::create([
            'author_id' => $author2->id,
            'name' => 'Post name b',
            'slug' => 'post-name-b',
            'views' => 30,
        ]);

        $post4 = SortPost::create([
            'author_id' => $author2->id,
            'name' => 'Post name y',
            'views' => 40,
        ]);

        $review1 = SortReview::create([
            'post_id' => $post1->id,
            'name' => 'Review a',
            'rating' => 4,
        ]);

        $review2 = SortReview::create([
            'post_id' => $post2->id,
            'name' => 'Review b',
            'rating' => 3,
        ]);

        $review3 = SortReview::create([
            'post_id' => $post3->id,
            'name' => 'Review c',
            'rating' => 2,
        ]);

        $review4 = SortReview::create([
            'post_id' => $post4->id,
            'name' => 'Review d',
            'rating' => 1,
        ]);
    }
}
