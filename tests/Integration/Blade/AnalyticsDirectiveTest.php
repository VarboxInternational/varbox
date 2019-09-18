<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Blade;
use Varbox\Models\Analytics;
use Varbox\Tests\Integration\TestCase;

class AnalyticsDirectiveTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_displays_the_analytics_code_by_using_the_blade_directive()
    {
        $directive = Blade::compileString('@analytics');
        $code = eval('return ' . str_replace(['<?php echo', '?>'], '', $directive) . ';');

        $this->assertEquals('', $code);

        Analytics::create(['code' => 'Test code']);

        $directive = Blade::compileString('@analytics');
        $code = eval('return ' . str_replace(['<?php echo', '?>'], '', $directive) . ';');

        $this->assertEquals('Test code', $code);
    }
}
