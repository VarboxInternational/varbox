<?php

namespace Varbox\Tests\Integration\Traits;

use Exception;
use Illuminate\Support\Str;
use Varbox\Options\UrlOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\UrlPost;

class HasUrlTest extends TestCase
{
    /**
     * @var UrlPost
     */
    protected $post;

    /** @test */
    public function it_generates_an_url_when_creating_a_record()
    {
        $this->createPost();

        $this->assertEquals('test-name', $this->post->url->url);
    }

    /** @test */
    public function it_updates_the_url_when_modifying_a_record()
    {
        $this->createPost();

        $this->post->update(['name' => 'Test name modified']);

        $this->assertEquals('test-name-modified', $this->post->url->url);
    }

    /** @test */
    public function it_creates_exactly_one_url_per_record()
    {
        $this->createPost();

        $this->post->update(['name' => 'Another test name']);

        $this->assertEquals(1, $this->post->url()->count());
    }

    /** @test */
    public function it_can_return_the_relative_url()
    {
        $this->createPost();

        $this->assertEquals('test-name', $this->post->getUri());
    }

    /** @test */
    public function it_can_return_the_absolute_url()
    {
        $this->createPost();

        $this->assertTrue(
            Str::startsWith($this->post->getUrl(), 'http') &&
            Str::endsWith($this->post->getUrl(), '/'.$this->post->getUri())
        );
    }

    /** @test */
    public function it_eager_loads_the_url_relation_for_the_given_eloquent_model()
    {
        $this->createPost();

        $this->assertTrue(array_key_exists(
            'url', UrlPost::find($this->post->id)->first()->relationsToArray()
        ));
    }

    /** @test */
    public function it_can_ignore_creating_an_url_if_specified()
    {
        $model = (new UrlPost)->doNotGenerateUrl()->create([
            'name' => 'Test name',
        ]);

        $this->assertEquals(0, $model->url()->count());
    }

    /** @test */
    public function it_can_ignore_updating_an_url_if_specified()
    {
        $this->createPost();

        $this->post->doNotGenerateUrl()->update([
            'name' => 'Modified test name',
        ]);

        $this->assertEquals('test-name', $this->post->url->url);
    }

    /** @test */
    public function it_has_a_method_that_allows_specifying_a_prefix_for_the_url()
    {
        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()->prefixUrlWith('prefix');
            }
        };

        $this->createPost($model, ['name' => 'String prefix test']);

        $this->assertEquals('prefix/string-prefix-test', $this->post->url->url);

        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()->prefixUrlWith(['array', 'prefix']);
            }
        };

        $this->createPost($model, ['name' => 'Array prefix test']);

        $this->assertEquals('array/prefix/array-prefix-test', $this->post->url->url);

        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()->prefixUrlWith(function ($prefix, $model) {
                    return implode('/', ['callable', 'prefix']);
                });
            }
        };

        $this->createPost($model, ['name' => 'Callable prefix test']);

        $this->assertEquals('callable/prefix/callable-prefix-test', $this->post->url->url);
    }

    /** @test */
    public function it_has_a_method_that_allows_specifying_a_suffix_for_the_url()
    {
        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()->suffixUrlWith('suffix');
            }
        };

        $this->createPost($model, ['name' => 'String suffix test']);

        $this->assertEquals('string-suffix-test/suffix', $this->post->url->url);

        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()->suffixUrlWith(['array', 'suffix']);
            }
        };

        $this->createPost($model, ['name' => 'Array suffix test']);

        $this->assertEquals('array-suffix-test/array/suffix', $this->post->url->url);

        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()->suffixUrlWith(function ($suffix, $model) {
                    return implode('/', ['callable', 'suffix']);
                });
            }
        };

        $this->createPost($model, ['name' => 'Callable suffix test']);

        $this->assertEquals('callable-suffix-test/callable/suffix', $this->post->url->url);
    }

    /** @test */
    public function it_has_a_method_that_allows_specifying_the_url_glue()
    {
        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return parent::getUrlOptions()
                    ->prefixUrlWith(['testing', 'glue'])
                    ->glueUrlWith('_');
            }
        };

        $this->createPost($model);

        $this->assertEquals('testing_glue_test-name', $this->post->url->url);
    }

    /** @expectedException Exception */
    public function it_expects_a_controller_and_action_to_be_specified_in_the_options()
    {
        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return UrlOptions::instance()
                    ->generateUrlSlugFrom('name')
                    ->saveUrlSlugTo('slug');
            }
        };

        $this->createPost($model);
    }

    /** @expectedException Exception */
    public function it_expects_a_from_field_to_be_specified_in_the_options()
    {
        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return UrlOptions::instance()
                    ->routeUrlTo('Controller', 'action')
                    ->saveUrlSlugTo('slug');
            }
        };

        $this->createPost($model);
    }

    /** @expectedException Exception */
    public function it_expects_a_to_field_to_be_specified_in_the_options()
    {
        $model = new class extends UrlPost {
            public function getUrlOptions() : UrlOptions
            {
                return UrlOptions::instance()
                    ->routeUrlTo('Controller', 'action')
                    ->generateUrlSlugFrom('name');
            }
        };

        $this->createPost($model);
    }

    /**
     * @param UrlPost|null $model
     * @param array $attributes
     */
    protected function createPost(UrlPost $model = null, $attributes = [])
    {
        $model = $model && $model instanceof UrlPost ? $model : new UrlPost;
        $attributes = $attributes && ! empty($attributes) ? $attributes : [
            'name' => 'Test name',
        ];

        $this->post = $model->create($attributes);
    }
}
