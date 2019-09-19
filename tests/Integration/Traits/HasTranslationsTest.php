<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Options\TranslationOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\TranslationPost;

class HasTranslationsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var TranslationPost
     */
    protected $post;

    /**
     * @var string
     */
    protected $postNameEn = 'Test post name';

    /**
     * @var string
     */
    protected $postNameRo = 'Nume post de test';

    /**
     * @var string
     */
    protected $postContentEn = 'Test post content';

    /**
     * @var string
     */
    protected $postContentRo = 'Continut post de test';

    /** @test */
    public function it_returns_the_attribute_value_for_the_current_locale()
    {
        $this->createPost();

        app()->setLocale('ro');

        $this->assertEquals($this->postNameRo, $this->post->name);
        $this->assertEquals($this->postContentRo, $this->post->content);

        app()->setLocale('en');

        $this->assertEquals($this->postNameEn, $this->post->name);
        $this->assertEquals($this->postContentEn, $this->post->content);
    }

    /** @test */
    public function it_returns_the_attribute_value_for_the_default_locale_if_an_unknown_locale_is_supplied()
    {
        $this->createPost();

        app()->setLocale('nl');

        $this->assertEquals($this->postNameEn, $this->post->name);
        $this->assertEquals($this->postContentEn, $this->post->content);

        $this->assertEquals($this->postNameEn, $this->post->translate('name', 'nl'));
        $this->assertEquals($this->postContentEn, $this->post->translate('content', 'nl'));
    }

    /** @test */
    public function it_mutates_the_translatable_value_for_an_attribute()
    {
        $this->createPost();

        app()->setLocale('en');

        $this->post->update([
            'name' => 'Test post name modified',
        ]);

        $this->assertEquals('Test post name modified', $this->post->name);
    }

    /** @test */
    public function it_returns_the_translated_value_of_an_attribute()
    {
        $this->createPost();

        app()->setLocale('en');

        $this->assertEquals($this->postNameRo, $this->post->translate('name', 'ro'));
        $this->assertEquals($this->postContentRo, $this->post->translate('content', 'ro'));

        app()->setLocale('ro');

        $this->assertEquals($this->postNameEn, $this->post->getTranslation('name', 'en'));
        $this->assertEquals($this->postContentEn, $this->post->translate('content', 'en'));
    }

    /** @test */
    public function it_can_return_all_translated_values_of_an_attribute()
    {
        $this->createPost();

        $translations = $this->post->getTranslations('name');

        $this->assertEquals($this->postNameEn, $translations['en']);
        $this->assertEquals($this->postNameRo, $translations['ro']);
    }

    /** @test */
    public function it_can_set_a_translatable_attribute_value()
    {
        $this->createPost();

        $this->post->setTranslation('name', 'Post name set', 'en');
        $this->post->setTranslation('content', 'Post content set', 'en');
        $this->post->save();

        $this->assertEquals('Post name set', $this->post->name);
        $this->assertEquals('Post content set', $this->post->content);
    }

    /** @test */
    public function it_can_set_a_translatable_attribute_value_that_has_a_mutator()
    {
        $model = new class extends TranslationPost {
            public function setNameAttribute($value)
            {
                $this->attributes['name'] = $value . ' mutated';
            }
        };

        $model->setTranslation('name', $this->postNameEn, 'en');
        $model->save();

        $this->assertEquals($this->postNameEn . ' mutated', $model->name);
    }

    /** @test */
    public function it_can_set_all_translatable_attribute_values_in_one_go()
    {
        $this->createPost();

        $this->post->setTranslations('name', [
            'en' => $this->postNameEn . ' one go',
            'ro' => $this->postNameRo . ' one go',
        ])->save();

        $this->assertEquals($this->postNameEn . ' one go', $this->post->translate('name', 'en'));
        $this->assertEquals($this->postNameRo . ' one go', $this->post->translate('name', 'ro'));
    }

    /** @test */
    public function it_can_forget_an_attribute_translation_for_a_specified_locale()
    {
        $this->createPost();

        $this->post->forgetTranslation('name', 'ro');
        $this->post->save();

        $this->assertArrayNotHasKey('ro', $this->post->getAttributeValue('name'));
        $this->assertArrayHasKey('ro', $this->post->getAttributeValue('content'));
    }

    /** @test */
    public function it_can_forget_all_translations_for_a_specified_locale()
    {
        $this->createPost();

        $this->post->forgetTranslations('ro');
        $this->post->save();

        $this->assertArrayNotHasKey('ro', $this->post->getAttributeValue('name'));
        $this->assertArrayNotHasKey('ro', $this->post->getAttributeValue('content'));
    }

    /** @test */
    public function it_can_determine_if_a_given_attribute_is_translatable()
    {
        $model = new TranslationPost;

        $this->assertTrue($model->isTranslatableAttribute('name'));
        $this->assertFalse($model->isTranslatableAttribute('slug'));
    }

    /** @test */
    public function it_reads_lower_levels_of_a_translatable_attribute_as_translatable()
    {
        $model = new TranslationPost;

        $this->assertTrue($model->isTranslatableAttribute('data[something]'));
    }

    /** @test */
    public function it_can_return_all_translatable_attributes()
    {
        $model = new TranslationPost;
        $attributes = $model->getTranslatableAttributes();

        $this->assertCount(3, $attributes);
        $this->assertEquals('name', $attributes[0]);
        $this->assertEquals('content', $attributes[1]);
        $this->assertEquals('data', $attributes[2]);

        $model = new class extends TranslationPost {
            public function getTranslationOptions() : TranslationOptions
            {
                return TranslationOptions::instance()
                    ->fieldsToTranslate('name');
            }
        };

        $attributes = $model->getTranslatableAttributes();

        $this->assertCount(1, $attributes);
        $this->assertEquals('name', $attributes[0]);
    }

    /** @test */
    public function it_can_return_all_translated_locales_for_an_attribute()
    {
        $this->createPost();

        $locales = $this->post->getTranslatedLocales('name');

        $this->assertCount(2, $locales);
        $this->assertEquals('en', $locales[0]);
        $this->assertEquals('ro', $locales[1]);
    }

    /** @test */
    public function it_throws_an_error_when_trying_to_get_a_translation_for_a_non_translatable_attribute()
    {
        $this->createPost();
        $this->expectExceptionMessage('Attribute "slug" is not translatable!');

        $this->post->getTranslation('slug', 'en');
    }

    /** @test */
    public function it_throws_an_error_when_trying_to_translate_a_non_translatable_attribute()
    {
        $this->createPost();
        $this->expectExceptionMessage('Attribute "slug" is not translatable!');

        $this->post->translate('slug', 'en');
    }

    /** @test */
    public function it_throws_an_error_when_trying_save_a_translation_for_a_non_translatable_attribute()
    {
        $this->createPost();
        $this->expectExceptionMessage('Attribute "slug" is not translatable!');

        $this->post->setTranslation('slug', 'some-value', 'en');
    }

    /**
     * @param TranslationPost|null $model
     * @return void
     */
    protected function createPost(TranslationPost $model = null)
    {
        $model = $model && $model instanceof TranslationPost ?
            $model : new TranslationPost;

        $this->post = $model->create([
            'name' => [
                'en' => $this->postNameEn,
                'ro' => $this->postNameRo,
            ],
            'slug' => 'test-post',
            'content' => [
                'en' => $this->postContentEn,
                'ro' => $this->postContentRo,
            ]
        ]);
    }
}
