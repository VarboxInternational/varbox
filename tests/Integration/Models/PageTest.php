<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Page;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasBlocks;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasNodes;
use Varbox\Traits\HasRevisions;
use Varbox\Traits\HasUploads;
use Varbox\Traits\HasUrl;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsDraftable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class PageTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var string
     */
    protected $pageName = 'Test Name';

    /**
     * @var string
     */
    protected $pageType = 'test-page';

    /**
     * @var string
     */
    protected $pageSlug = 'test-slug';

    /**
     * @var string
     */
    protected $pageController = 'TestPageController';

    /**
     * @var string
     */
    protected $pageAction = 'show';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('varbox.pages.types', [
            $this->pageType => [
                'controller' => $this->pageController,
                'action' => $this->pageAction,
            ]
        ]);
    }

    /** @test */
    public function it_uses_the_has_url_trait()
    {
        $this->assertArrayHasKey(HasUrl::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_has_uploads_trait()
    {
        $this->assertArrayHasKey(HasUploads::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_has_revisions_trait()
    {
        $this->assertArrayHasKey(HasRevisions::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_has_duplicates_trait()
    {
        $this->assertArrayHasKey(HasDuplicates::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_has_blocks_trait()
    {
        $this->assertArrayHasKey(HasBlocks::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_has_nodes_trait()
    {
        $this->assertArrayHasKey(HasNodes::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_is_draftable_trait()
    {
        $this->assertArrayHasKey(IsDraftable::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Page::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Page::class));
    }

    /** @test */
    public function it_can_return_the_page_controller()
    {
        $this->createPage();

        $this->assertEquals($this->pageController, $this->page->route_controller);
    }

    /** @test */
    public function it_can_return_the_page_action()
    {
        $this->createPage();

        $this->assertEquals($this->pageAction, $this->page->route_action);
    }

    /** @test */
    public function it_can_filter_records_by_parent()
    {
        $parent1 = Page::create([
            'name' => 'Parent 1',
            'slug' => 'parent-1',
            'type' => $this->pageType
        ]);

        $parent2 = Page::create([
            'name' => 'Parent-2',
            'slug' => 'parent-2',
            'type' => $this->pageType
        ]);

        Page::create([
            'name' => 'Child 1',
            'slug' => 'child-1',
            'type' => $this->pageType
        ], $parent1);

        Page::create([
            'name' => 'Child 2',
            'slug' => 'child-2',
            'type' => $this->pageType
        ], $parent1);

        Page::create([
            'name' => 'Child 3',
            'slug' => 'child-3',
            'type' => $this->pageType
        ], $parent2);

        $parent1Pages = Page::ofParent($parent1)->get();
        $parent2Pages = Page::ofParent($parent2)->get();

        $this->assertEquals(2, $parent1Pages->count());
        $this->assertEquals('Child 1', $parent1Pages->first()->name);
        $this->assertEquals('Child 2', $parent1Pages->last()->name);

        $this->assertEquals(1, $parent2Pages->count());
        $this->assertEquals('Child 3', $parent2Pages->first()->name);
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        Page::create([
            'name' => 'Some page',
            'slug' => 'some-page',
            'type' => $this->pageType
        ]);

        Page::create([
            'name' => 'Another page',
            'slug' => 'another-page',
            'type' => $this->pageType
        ]);

        Page::create([
            'name' => 'The page',
            'slug' => 'the-page',
            'type' => $this->pageType
        ]);

        $this->assertEquals('Another page', Page::alphabetically()->get()->first()->name);
        $this->assertEquals('The page', Page::alphabetically()->get()->last()->name);
    }

    /**
     * @return void
     */
    protected function createPage()
    {
        $this->page = Page::create([
            'name' => $this->pageName,
            'slug' => $this->pageSlug,
            'type' => $this->pageType,
        ]);
    }
}
