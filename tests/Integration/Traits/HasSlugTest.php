<?php

namespace Varbox\Tests\Integration\Traits;

use Exception;
use Varbox\Options\SlugOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Author;

class HasSlugTest extends TestCase
{
    /**
     * @var Author
     */
    protected $author;

    /** @test */
    public function it_generates_a_slug_when_creating_a_record()
    {
        $this->createAuthor();

        $this->assertEquals('test-author', $this->author->slug);
    }

    /** @test */
    public function it_updates_a_slug_when_modifying_a_record()
    {
        $this->createAuthor();

        $this->author->update(['name' => 'Modified test author']);

        $this->assertEquals('modified-test-author', $this->author->slug);
    }

    /** @test */
    public function it_saves_unique_slugs_for_each_record_by_default()
    {
        $this->createAuthor();

        foreach (range(1, 10) as $i) {
            $this->createAuthor();

            $this->assertEquals("test-author-{$i}", $this->author->slug);
        }
    }

    /** @test */
    public function it_has_a_method_preventing_a_slug_from_being_generated_on_create()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->doNotGenerateSlugOnCreate();
            }
        };

        $this->createAuthor($model);

        $this->assertEquals(null, $this->author->slug);
    }

    /** @test */
    public function it_has_a_method_preventing_a_slug_from_being_generated_on_update()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->doNotGenerateSlugOnUpdate();
            }
        };

        $this->createAuthor($model);

        $this->author->update(['name' => 'Modified test author']);

        $this->assertEquals('test-author', $this->author->slug);
    }

    /**
     * Not generating unique slugs and allowing duplicates is possible by customizing the HasSlug behavior, via SlugOptions.
     * SlugOptions::instance()->allowDuplicateSlugs().
     *
     * @test
     */
    public function it_has_a_method_that_allows_saving_duplicate_slugs()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->allowDuplicateSlugs();
            }
        };

        foreach (range(1, 10) as $i) {
            $this->createAuthor($model);

            $this->assertEquals('test-author', $this->author->slug);
        }
    }

    /** @test */
    public function it_has_a_method_for_manually_defining_the_word_separator()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->usingSeparator('_');
            }
        };

        $this->createAuthor($model);

        $this->assertEquals('test_author', $this->author->slug);
    }

    /** @test */
    public function it_can_generate_slugs_from_multiple_source_fields()
    {
        $model = new class extends Author {
            public function getSlugOptions(): SlugOptions
            {
                return parent::getSlugOptions()->generateSlugFrom([
                    'name', 'title',
                ]);
            }
        };

        $this->createAuthor($model);

        $this->assertEquals('test-author-mr', $this->author->slug);
    }

    /** @test */
    public function it_can_generate_language_specific_slugs()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions();
            }
        };

        $this->createAuthor($model, ['name' => 'Güte nacht']);

        $this->assertEquals('gute-nacht', $this->author->slug);

        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return parent::getSlugOptions()->usingLanguage('de');
            }
        };

        $this->createAuthor($model, ['name' => 'Güte nacht']);

        $this->assertEquals('guete-nacht', $this->author->slug);
    }

    /** @expectedException Exception */
    public function it_expects_a_from_field_to_be_specified_in_the_options()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return SlugOptions::instance()->saveSlugTo('slug');
            }
        };

        $this->createAuthor($model);
    }

    /** @expectedException Exception */
    public function it_expects_a_to_field_to_be_specified_in_the_options()
    {
        $model = new class extends Author {
            public function getSlugOptions() : SlugOptions
            {
                return SlugOptions::instance()->generateSlugFrom('slug');
            }
        };

        $this->createAuthor($model);
    }

    /**
     * @param Author|null $model
     * @param array $attributes
     */
    protected function createAuthor(Author $model = null, $attributes = [])
    {
        $model = $model && $model instanceof Author ? $model : new Author;
        $attributes = $attributes && ! empty($attributes) ? $attributes : [
            'title' => 'Mr.',
            'name' => 'Test author',
            'age' => '30',
        ];

        $this->author = $model->create($attributes);
    }
}
