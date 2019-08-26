<?php

namespace Varbox\Tests\Integration\Traits;

use Exception;
use Varbox\Options\SlugOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\SlugPost;

class HasSlugTest extends TestCase
{
    /**
     * @var SlugPost
     */
    protected $post;

    /** @test */
    public function it_generates_a_slug_when_creating_a_record()
    {
        $this->createPost();

        $this->assertEquals('test-post', $this->post->slug);
    }

    /** @test */
    public function it_updates_a_slug_when_modifying_a_record()
    {
        $this->createPost();

        $this->post->update(['name' => 'Modified test post']);

        $this->assertEquals('modified-test-post', $this->post->slug);
    }

    /** @test */
    public function it_saves_unique_slugs_for_each_record_by_default()
    {
        $this->createPost();

        foreach (range(1, 10) as $i) {
            $this->createPost();

            $this->assertEquals("test-post-{$i}", $this->post->slug);
        }
    }

    /** @test */
    public function it_has_a_method_preventing_a_slug_from_being_generated_on_create()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->doNotGenerateSlugOnCreate();
            }
        };

        $this->createPost($model);

        $this->assertEquals(null, $this->post->slug);
    }

    /** @test */
    public function it_has_a_method_preventing_a_slug_from_being_generated_on_update()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->doNotGenerateSlugOnUpdate();
            }
        };

        $this->createPost($model);

        $this->post->update(['name' => 'Modified test post']);

        $this->assertEquals('test-post', $this->post->slug);
    }

    /**
     * Not generating unique slugs and allowing duplicates is possible by customizing the HasSlug behavior, via SlugOptions.
     * SlugOptions::instance()->allowDuplicateSlugs().
     *
     * @test
     */
    public function it_has_a_method_that_allows_saving_duplicate_slugs()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->allowDuplicateSlugs();
            }
        };

        foreach (range(1, 10) as $i) {
            $this->createPost($model);

            $this->assertEquals('test-post', $this->post->slug);
        }
    }

    /** @test */
    public function it_has_a_method_for_manually_defining_the_word_separator()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->usingSeparator('_');
            }
        };

        $this->createPost($model);

        $this->assertEquals('test_post', $this->post->slug);
    }

    /** @test */
    public function it_can_generate_slugs_from_multiple_source_fields()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions(): SlugOptions
            {
                return parent::getSlugOptions()->generateSlugFrom([
                    'name', 'title',
                ]);
            }
        };

        $this->createPost($model);

        $this->assertEquals('test-post-mr', $this->post->slug);
    }

    /** @test */
    public function it_can_generate_language_specific_slugs()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions();
            }
        };

        $this->createPost($model, ['name' => 'Güte nacht']);

        $this->assertEquals('gute-nacht', $this->post->slug);

        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->usingLanguage('de');
            }
        };

        $this->createPost($model, ['name' => 'Güte nacht']);

        $this->assertEquals('guete-nacht', $this->post->slug);
    }

    /** @expectedException Exception */
    public function it_expects_a_from_field_to_be_specified_in_the_options()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return SlugOptions::instance()->saveSlugTo('slug');
            }
        };

        $this->createPost($model);
    }

    /** @expectedException Exception */
    public function it_expects_a_to_field_to_be_specified_in_the_options()
    {
        $model = new class extends SlugPost {
            public function getSlugOptions() : SlugOptions
            {
                return SlugOptions::instance()->generateSlugFrom('slug');
            }
        };

        $this->createPost($model);
    }

    /**
     * @param SlugPost|null $model
     * @param array $attributes
     */
    protected function createPost(SlugPost $model = null, $attributes = [])
    {
        $model = $model && $model instanceof SlugPost ? $model : new SlugPost;
        $attributes = $attributes && ! empty($attributes) ? $attributes : [
            'title' => 'Mr',
            'name' => 'Test post',
        ];

        $this->post = $model->create($attributes);
    }
}
