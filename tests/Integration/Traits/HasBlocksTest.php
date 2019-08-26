<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Varbox\Models\Block;
use Varbox\Options\BlockOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\BlockAuthor;
use Varbox\Tests\Models\BlockPost;

class HasBlocksTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var BlockAuthor
     */
    protected $author;

    /**
     * @var BlockPost
     */
    protected $post;

    /**
     * @var array
     */
    protected $blocks = [];

    /**
     * @var string
     */
    protected $blockType = 'Example';

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.blocks.types', [
            $this->blockType => [
                'label' => 'Example Block',
                'composer_class' => "App\Blocks\\" . $this->blockType . "\Composer",
                'views_path' => "app/Blocks/" . $this->blockType . "/Views",
            ]
        ]);
    }

    /** @test */
    public function it_can_get_all_blocks_in_a_location()
    {
        $this->createBlocks();
        $this->createPost();

        $this->post->saveBlocks($this->blocks);

        $headerBlocks = $this->post->getBlocksInLocation('header');
        $contentBlocks = $this->post->getBlocksInLocation('content');
        $otherBlocks = $this->post->getBlocksInLocation('other');

        $this->assertEquals(1, $headerBlocks->count());
        $this->assertEquals(2, $contentBlocks->count());
        $this->assertEquals(0, $otherBlocks->count());
    }

    /** @test */
    public function it_can_return_block_locations_of_a_model()
    {
        $this->createPost();

        $locations = $this->post->getBlockLocations();

        $this->assertCount(2, $locations);
        $this->assertEquals('header', Arr::first($locations));
        $this->assertEquals('content', Arr::last($locations));
    }

    /** @test */
    public function it_can_save_blocks()
    {
        $this->createBlocks();
        $this->createPost();

        $this->assertEquals(0, $this->post->blocks()->count());

        $this->post->saveBlocks($this->blocks);

        $this->assertEquals(3, $this->post->blocks()->count());

        foreach ($this->post->blocks as $index => $block) {
            $this->assertEquals($block->id, key($this->blocks[$index]));
            $this->assertEquals($block->pivot->location, $this->blocks[$index][$block->id]['location']);
            $this->assertEquals($block->pivot->ord, $this->blocks[$index][$block->id]['ord']);
        }
    }

    /** @test */
    public function it_can_assign_a_block()
    {
        $this->createPost();

        $block = Block::create([
            'type' => $this->blockType,
            'name' => 'Test block'
        ]);

        $this->post->assignBlock($block, 'content');

        $this->assertEquals(1, $this->post->blocks()->count());
        $this->assertEquals($block->id, $this->post->blocks()->first()->id);
        $this->assertEquals('content', $this->post->blocks()->first()->pivot->location);
    }

    /** @test */
    public function it_can_unassign_a_block()
    {
        $this->createPost();

        $block = Block::create([
            'type' => $this->blockType,
            'name' => 'Test block'
        ]);

        $this->post->assignBlock($block, 'content');

        $this->assertEquals(1, $this->post->blocks()->count());

        $this->post->unassignBlock($block, 'content', $this->post->blocks()->first()->pivot->id);

        $this->assertEquals(0, $this->post->blocks()->count());
    }

    /** @test */
    public function it_can_get_inherited_blocks_from_a_string()
    {
        $model = new class extends BlockPost {
            public function getBlockOptions() : BlockOptions
            {
                return parent::getBlockOptions()->inheritFrom('author');
            }
        };

        $this->createAuthor();
        $this->createPost($model);

        $block = Block::create([
            'type' => $this->blockType,
            'name' => 'Test block'
        ]);

        $this->author->saveBlocks([
            [
                $block->id => [
                    'location' => 'header',
                    'ord' => 1,
                ],
            ]
        ]);

        $this->assertEquals(0, $this->post->getBlocksInLocation('header')->count());
        $this->assertEquals(1, $this->post->getInheritedBlocks('header')->count());
    }

    /** @test */
    public function it_can_get_inherited_blocks_from_a_model_instance()
    {
        $this->createAuthor();

        $block = Block::create([
            'type' => $this->blockType,
            'name' => 'Test block'
        ]);

        $this->author->saveBlocks([
            [
                $block->id => [
                    'location' => 'header',
                    'ord' => 1,
                ],
            ]
        ]);

        $model = new class extends BlockPost {
            public function getBlockOptions() : BlockOptions
            {
                return parent::getBlockOptions()->inheritFrom(BlockAuthor::first());
            }
        };

        $this->createPost($model);

        $this->assertEquals(0, $this->post->getBlocksInLocation('header')->count());
        $this->assertEquals(1, $this->post->getInheritedBlocks('header')->count());
    }

    /**
     * @param BlockPost|null $model
     * @return void
     */
    protected function createPost(BlockPost $model = null)
    {
        $model = $model && $model instanceof BlockPost ? $model : new BlockPost;

        $this->post = $model->create([
            'author_id' => optional($this->author)->id ?: null,
            'name' => 'Test post',
        ]);
    }

    /**
     * @return void
     */
    protected function createAuthor()
    {
        $this->author = BlockAuthor::create([
            'name' => 'Test author',
        ]);
    }

    /**
     * @return void
     */
    protected function createBlocks()
    {
        $block1 = Block::create([
            'type' => $this->blockType,
            'name' => 'Block 1'
        ]);

        $block2 = Block::create([
            'type' => $this->blockType,
            'name' => 'Block 2'
        ]);

        $block3 = Block::create([
            'type' => $this->blockType,
            'name' => 'Block 3'
        ]);

        $this->blocks = [
            [
                $block1->id => [
                    'location' => 'header',
                    'ord' => 1,
                ],
            ],
            [
                $block2->id => [
                    'location' => 'content',
                    'ord' => 1,
                ],
            ],
            [
                $block3->id => [
                    'location' => 'content',
                    'ord' => 2,
                ],
            ],
        ];
    }
}
