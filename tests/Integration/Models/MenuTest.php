<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Varbox\Models\Menu;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\MenuPost;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasNodes;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class MenuTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Menu
     */
    protected $menu;

    /**
     * @var MenuPost
     */
    protected $post;

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.menus.types', [
            'post' => MenuPost::class,
        ]);
    }

    /** @test */
    public function it_uses_the_has_nodes_trait()
    {
        $this->assertArrayHasKey(HasNodes::class, class_uses(Menu::class));
    }

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Menu::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Menu::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Menu::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Menu::class));
    }

    /** @test */
    public function it_stores_only_the_url_when_saving_with_the_url_type()
    {
        $this->createMenu('url');

        $this->assertEquals('test-url', $this->menu->getOriginal('url'));
        $this->assertNull($this->menu->getOriginal('route'));
        $this->assertNull($this->menu->getOriginal('menuable_id'));
        $this->assertNull($this->menu->getOriginal('menuable_type'));
    }

    /** @test */
    public function it_stores_only_the_route_when_saving_with_the_route_type()
    {
        $this->createMenu('route');

        $this->assertEquals('test.route', $this->menu->getOriginal('route'));
        $this->assertNull($this->menu->getOriginal('url'));
        $this->assertNull($this->menu->getOriginal('menuable_id'));
        $this->assertNull($this->menu->getOriginal('menuable_type'));
    }

    /** @test */
    public function it_stores_only_the_menuable_when_saving_with_the_menuable_type()
    {
        $this->createPost();
        $this->createMenu('post');

        $this->assertEquals($this->post->id, $this->menu->getOriginal('menuable_id'));
        $this->assertEquals($this->post->getMorphClass(), $this->menu->getOriginal('menuable_type'));
        $this->assertNull($this->menu->getOriginal('route'));
        $this->assertNull($this->menu->getOriginal('url'));
    }

    /** @test */
    public function it_can_return_the_full_url_of_a_menu_of_type_url()
    {
        $this->createMenu('url');

        $this->assertEquals($this->baseUrl . '/test-url', $this->menu->url);
    }

    /** @test */
    public function it_can_return_the_partial_uri_of_a_menu_of_type_url()
    {
        $this->createMenu('url');

        $this->assertEquals('/test-url', $this->menu->uri);
    }

    /** @test */
    public function it_can_return_the_full_url_of_a_menu_of_type_route()
    {
        Route::get('/_test/menu-route', ['as' => 'test.route']);

        $this->createMenu('route');

        $this->assertEquals($this->baseUrl . '/_test/menu-route', $this->menu->url);
    }

    /** @test */
    public function it_can_return_the_partial_uri_of_a_menu_of_type_route()
    {
        Route::get('/_test/menu-route', ['as' => 'test.route']);

        $this->createMenu('route');

        $this->assertEquals('/_test/menu-route', $this->menu->uri);
    }

    /** @test */
    public function it_can_return_the_full_url_of_a_menu_of_type_custom()
    {
        $this->createPost();
        $this->createMenu('post');

        $this->assertEquals($this->post->getUrl(), $this->menu->url);
    }

    /** @test */
    public function it_can_return_the_partial_uri_of_a_menu_of_type_custom()
    {
        $this->createPost();
        $this->createMenu('post');

        $this->assertEquals($this->post->getUri(), $this->menu->uri);
    }

    /** @test */
    public function it_can_get_only_records_of_a_parent()
    {
        $parent1 = Menu::create(['name' => 'Menu parent 1']);
        $parent2 = Menu::create(['name' => 'Menu parent 2']);

        $child1 = Menu::create(['name' => 'Menu child 1'], $parent1);
        $child2 = Menu::create(['name' => 'Menu child 2'], $parent1);
        $child3 = Menu::create(['name' => 'Menu child 3'], $parent2);

        $childrenOfParent1 = Menu::ofParent($parent1)->get();
        $childrenOfParent2 = Menu::ofParent($parent2)->get();

        $this->assertEquals(2, $childrenOfParent1->count());
        $this->assertEquals($child1->name, $childrenOfParent1->first()->name);
        $this->assertEquals($child2->name, $childrenOfParent1->last()->name);

        $this->assertEquals(1, $childrenOfParent2->count());
        $this->assertEquals($child3->name, $childrenOfParent2->first()->name);
    }

    /** @test */
    public function it_can_get_only_active_records()
    {
        Menu::create(['name' => 'Menu 1', 'active' => true]);
        Menu::create(['name' => 'Menu 2', 'active' => false]);

        $records = Menu::onlyActive()->get();

        $this->assertEquals(1, $records->count());
        $this->assertEquals('Menu 1', $records->first()->name);
    }

    /** @test */
    public function it_can_get_only_inactive_records()
    {
        Menu::create(['name' => 'Menu 1', 'active' => true]);
        Menu::create(['name' => 'Menu 2', 'active' => false]);

        $records = Menu::onlyInactive()->get();

        $this->assertEquals(1, $records->count());
        $this->assertEquals('Menu 2', $records->first()->name);
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        Menu::create(['name' => 'Some block']);
        Menu::create(['name' => 'Another block']);
        Menu::create(['name' => 'The block']);

        $this->assertEquals('Another block', Menu::alphabetically()->get()->first()->name);
        $this->assertEquals('The block', Menu::alphabetically()->get()->last()->name);
    }

    /** @test */
    public function it_can_return_the_application_routes()
    {
        Route::get('/_test/post-route', ['as' => 'test.route'])->middleware('web');

        $this->assertEquals(1, count(Menu::getRoutes()));
    }

    /** @test */
    public function it_doesnt_return_non_get_routes()
    {
        Route::post('/_test/post-route', ['as' => 'test.route'])->middleware('web');

        $this->assertEquals(0, count(Menu::getRoutes()));
    }

    /** @test */
    public function it_doesnt_return_routes_without_a_name()
    {
        Route::get('/_test/no-name-route')->middleware('web');

        $this->assertEquals(0, count(Menu::getRoutes()));
    }

    /** @test */
    public function it_doesnt_return_routes_without_the_web_middleware()
    {
        Route::get('/_test/post-route', ['as' => 'test.route']);

        $this->assertEquals(0, count(Menu::getRoutes()));
    }

    /** @test */
    public function it_doesnt_return_routes_with_parameters_in_their_uris()
    {
        Route::get('/_test/post-route/{parameter}', ['as' => 'test.route'])->middleware('web');

        $this->assertEquals(0, count(Menu::getRoutes()));
    }

    /** @test */
    public function it_doesnt_return_routes_from_the_admin()
    {
        Route::get(config('varbox.admin.prefix', 'admin') . '/_test/post-route', ['as' => 'test.route'])->middleware('web');

        $this->assertEquals(0, count(Menu::getRoutes()));
    }

    /**
     * @param string $type
     * @return void
     */
    protected function createMenu($type)
    {
        $this->menu = Menu::create([
            'type' => $type,
            'name' => 'Test name',
            'url' => 'test-url',
            'route' => 'test.route',
            'menuable_id' => $this->post ? $this->post->id : null,
            'menuable_type' => $this->post ? $this->post->getMorphClass() : null,
        ]);
    }

    /**
     * @return void
     */
    protected function createPost()
    {
        $this->post = MenuPost::create([
            'name' => 'Test menu post'
        ]);
    }
}
