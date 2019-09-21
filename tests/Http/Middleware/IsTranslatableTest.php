<?php

namespace Varbox\Tests\Http\Middleware;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Varbox\Middleware\IsTranslatable;
use Varbox\Models\Language;
use Varbox\Tests\Http\TestCase;

class IsTranslatableTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/_test/is-not-translatable', function () {
            return defined('IS_TRANSLATABLE') && IS_TRANSLATABLE === true ? 'true' : 'false';
        });

        Route::middleware(IsTranslatable::class)->get('/_test/is-translatable', function () {
            return defined('IS_TRANSLATABLE') && IS_TRANSLATABLE === true ? 'true' : 'false';
        });
    }

    /** @test */
    public function it_can_determine_if_an_entity_is_not_translatable()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/_test/is-not-translatable');
        $this->assertEquals('false', $response->getContent());
    }

    /** @test */
    public function it_can_determine_if_an_entity_is_translatable()
    {
        $this->withoutExceptionHandling();

        Language::create([
            'name' => 'English',
            'code' => 'en',
            'active' => true,
            'default' => true,
        ]);

        $response = $this->get('/_test/is-translatable');
        $this->assertEquals('true', $response->getContent());
    }
}
