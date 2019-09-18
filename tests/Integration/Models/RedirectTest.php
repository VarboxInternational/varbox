<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Exceptions\RedirectException;
use Varbox\Models\Redirect;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class RedirectTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Redirect::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Redirect::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Redirect::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Redirect::class));
    }

    /** @test */
    public function it_guards_against_creating_redirect_loops()
    {
        $this->expectException(RedirectException::class);

        Redirect::create([
            'old_url' => 'same-url',
            'new_url' => 'same-url',
        ]);
    }

    /** @test */
    public function it_syncs_old_urls_with_the_latest_value()
    {
        Redirect::create([
            'old_url' => '1',
            'new_url' => '2',
        ]);

        Redirect::create([
            'old_url' => '2',
            'new_url' => '3',
        ]);

        Redirect::create([
            'old_url' => '3',
            'new_url' => '4',
        ]);

        $this->assertEquals('4', Redirect::whereOldUrl('1')->first()->new_url);
        $this->assertEquals('4', Redirect::whereOldUrl('2')->first()->new_url);
    }

    /** @test */
    public function it_trims_the_old_url_attribute()
    {
        $redirect = Redirect::create([
            'old_url' => '/',
            'new_url' => 'new-url',
        ]);

        $this->assertEquals('/', $redirect->old_url);

        $redirect->update([
            'old_url' => '/old-url/',
        ]);

        $this->assertEquals('old-url', $redirect->old_url);
    }

    /** @test */
    public function it_trims_the_new_url_attribute()
    {
        $redirect = Redirect::create([
            'old_url' => 'old-url',
            'new_url' => '/',
        ]);

        $this->assertEquals('/', $redirect->new_url);

        $redirect->update([
            'new_url' => '/new-url/',
        ]);

        $this->assertEquals('new-url', $redirect->new_url);
    }

    /** @test */
    public function it_can_find_a_valid_redirect()
    {
        Redirect::create([
            'old_url' => 'the-old-url',
            'new_url' => 'the/new-url',
        ]);

        $redirect = Redirect::findValidOrNull('/the-old-url/');

        $this->assertEquals('the/new-url', $redirect->new_url);
    }

    /** @test */
    public function it_reads_a_redirect_without_a_valid_status_as_invalid()
    {
        Redirect::create([
            'old_url' => 'the-old-url',
            'new_url' => 'the/new-url',
            'status' => '404',
        ]);

        $redirect = Redirect::findValidOrNull('/the-old-url/');

        $this->assertNull($redirect);
    }

    /** @test */
    public function it_reads_a_redirect_without_a_filled_new_url_as_invalid()
    {
        Redirect::create([
            'old_url' => 'the-old-url',
            'new_url' => '',
        ]);

        $redirect = Redirect::findValidOrNull('/the-old-url/');

        $this->assertNull($redirect);
    }
}
