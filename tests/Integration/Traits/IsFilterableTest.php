<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Filters\Filter;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Post;

class IsFilterableTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Post
     */
    protected $post1;

    /**
     * @var Post
     */
    protected $post2;

    /**
     * @var Post
     */
    protected $post3;

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
    public function it_can_filter_records_using_the_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => $this->post1->name,
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_not_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_NOT_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => $this->post1->name,
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post2->name, $posts->first()->name);
        $this->assertEquals($this->post3->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_smaller_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'votes' => [
                        'operator' => Filter::OPERATOR_SMALLER,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'votes' => $this->post2->votes,
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_greater_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'votes' => [
                        'operator' => Filter::OPERATOR_GREATER,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'votes' => $this->post2->votes,
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post3->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_smaller_or_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'votes' => [
                        'operator' => Filter::OPERATOR_SMALLER_OR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'votes' => $this->post2->votes,
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
        $this->assertEquals($this->post2->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_greater_or_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'votes' => [
                        'operator' => Filter::OPERATOR_GREATER_OR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'votes' => $this->post2->votes,
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post2->name, $posts->first()->name);
        $this->assertEquals($this->post3->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_null_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'author' => [
                        'operator' => Filter::OPERATOR_NULL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'author_id'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'author' => '',
        ], $filter)->get();

        $this->assertEquals(3, $posts->count());
    }

    /** @test */
    public function it_can_filter_records_using_the_not_null_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'author' => [
                        'operator' => Filter::OPERATOR_NOT_NULL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'author_id'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'author' => '',
        ], $filter)->get();

        $this->assertEquals(0, $posts->count());
    }

    /** @test */
    public function it_can_filter_records_using_the_like_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_LIKE,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => substr($this->post2->name, 0, 4),
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post2->name, $posts->first()->name);
        $this->assertEquals($this->post3->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_not_like_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_NOT_LIKE,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => substr($this->post2->name, 0, 4),
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_in_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'id' => [
                        'operator' => Filter::OPERATOR_IN,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'id'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'id' => [
                $this->post1->id,
                $this->post2->id
            ],
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
        $this->assertEquals($this->post2->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_not_in_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'id' => [
                        'operator' => Filter::OPERATOR_NOT_IN,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'id'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'id' => [
                $this->post1->id,
                $this->post2->id
            ],
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post3->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_between_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'votes' => [
                        'operator' => Filter::OPERATOR_BETWEEN,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'votes' => [
                $this->post1->votes,
                $this->post2->votes
            ],
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
        $this->assertEquals($this->post2->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_not_between_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'votes' => [
                        'operator' => Filter::OPERATOR_NOT_BETWEEN,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'votes' => [
                $this->post1->votes,
                $this->post2->votes
            ],
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post3->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_date_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'date' => [
                        'operator' => Filter::OPERATOR_DATE_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'published_at'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'date' => $this->post1->published_at->format('Y-m-d'),
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_date_not_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'date' => [
                        'operator' => Filter::OPERATOR_DATE_NOT_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'published_at'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'date' => $this->post1->published_at->format('Y-m-d'),
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post2->name, $posts->first()->name);
        $this->assertEquals($this->post3->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_date_smaller_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'date' => [
                        'operator' => Filter::OPERATOR_DATE_SMALLER,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'published_at'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'date' => $this->post1->published_at->format('Y-m-d'),
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post3->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_date_greater_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'date' => [
                        'operator' => Filter::OPERATOR_DATE_GREATER,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'published_at'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'date' => $this->post1->published_at->format('Y-m-d'),
        ], $filter)->get();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($this->post2->name, $posts->first()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_date_smaller_or_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'date' => [
                        'operator' => Filter::OPERATOR_DATE_SMALLER_OR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'published_at'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'date' => $this->post1->published_at->format('Y-m-d'),
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
        $this->assertEquals($this->post3->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_using_the_date_greater_or_equal_operator()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'date' => [
                        'operator' => Filter::OPERATOR_DATE_GREATER_OR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'published_at'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'date' => $this->post1->published_at->format('Y-m-d'),
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->name, $posts->first()->name);
        $this->assertEquals($this->post2->name, $posts->last()->name);
    }

    /** @test */
    public function it_can_filter_records_by_morphing_conditions()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_LIKE,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name'
                    ],
                    'votes' => [
                        'operator' => Filter::OPERATOR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => substr($this->post2->name, 0, 4),
            'votes' => 10,
        ], $filter)->get();

        $this->assertEquals(0, $posts->count());

        $filter = new class extends Filter {
            public function morph()
            {
                return 'or';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_LIKE,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name'
                    ],
                    'votes' => [
                        'operator' => Filter::OPERATOR_EQUAL,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'votes'
                    ]
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => substr($this->post2->name, 0, 4),
            'votes' => 10,
        ], $filter)->get();

        $this->assertEquals(3, $posts->count());
    }

    /** @test */
    public function it_supports_multiple_columns_for_a_single_filter_field()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_LIKE,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name,votes'
                    ],
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => 1,
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->id, $posts->first()->id);
        $this->assertEquals($this->post3->id, $posts->last()->id);
    }

    /** @test */
    public function it_supports_inter_column_conditioning_for_a_single_filter_field()
    {
        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_LIKE,
                        'condition' => Filter::CONDITION_OR,
                        'columns' => 'name,votes'
                    ],
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => 1,
        ], $filter)->get();

        $this->assertEquals(2, $posts->count());
        $this->assertEquals($this->post1->id, $posts->first()->id);
        $this->assertEquals($this->post3->id, $posts->last()->id);

        $filter = new class extends Filter {
            public function morph()
            {
                return 'and';
            }

            public function filters()
            {
                return [
                    'name' => [
                        'operator' => Filter::OPERATOR_LIKE,
                        'condition' => Filter::CONDITION_AND,
                        'columns' => 'name,votes'
                    ],
                ];
            }

            public function modifiers()
            {
                return [];
            }
        };

        $posts = Post::filtered([
            'name' => 1,
        ], $filter)->get();

        $this->assertEquals(0, $posts->count());
    }

    /**
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $this->post1 = Post::create([
            'name' => 'The Test Post',
            'votes' => '10',
            'published_at' => today(),
        ]);

        $this->post2 = Post::create([
            'name' => 'Some Test Post',
            'votes' => '50',
            'published_at' => today()->addDays(10),
        ]);

        $this->post3 = Post::create([
            'name' => 'Some Another Test Post',
            'votes' => '100',
            'published_at' => today()->subDays(10),
        ]);
    }
}
