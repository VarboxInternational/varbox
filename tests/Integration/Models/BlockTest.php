<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Block;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasRevisions;
use Varbox\Traits\HasUploads;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsDraftable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class BlockTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Block
     */
    protected $block;

    /** @test */
    public function it_uses_the_has_uploads_trait()
    {
        $this->assertArrayHasKey(HasUploads::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_has_revisions_trait()
    {
        $this->assertArrayHasKey(HasRevisions::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_has_duplicates_trait()
    {
        $this->assertArrayHasKey(HasDuplicates::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_is_draftable_trait()
    {
        $this->assertArrayHasKey(IsDraftable::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Block::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Block::class));
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        Block::create(['type' => 'ExampleBlock', 'name' => 'Some block']);
        Block::create(['type' => 'ExampleBlock', 'name' => 'Another block']);
        Block::create(['type' => 'ExampleBlock', 'name' => 'The block']);

        $this->assertEquals('Another block', Block::alphabetically()->get()->first()->name);
        $this->assertEquals('The block', Block::alphabetically()->get()->last()->name);
    }
}
