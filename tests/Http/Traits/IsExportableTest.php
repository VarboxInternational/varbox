<?php

namespace Varbox\Tests\Http\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\CsvPost;

class IsExportableTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_download_an_exported_csv_file()
    {
        $this->withoutExceptionHandling();
        $this->createPost();

        Route::get('/_test/export-csv', function () {
            return app(CsvPost::class)->exportToCsv(CsvPost::all());
        });

        $response = $this->get('/_test/export-csv');
        $filename = now()->format('Y-m-d-his') . '-csv_posts.csv';

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=' . $filename);
    }

    /**
     * @return void
     */
    protected function createPost()
    {
        CsvPost::create([
            'name' => 'Post test name',
        ]);
    }
}
