<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Varbox\Tests\Integration\TestCase;

class MailMakeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var string
     */
    protected $mailType;

    /**
     * @var string
     */
    protected $mailClass;

    /**
     * @var string
     */
    protected $mailView;

    public function setUp(): void
    {
        parent::setUp();

        $this->mailType = 'test-mail';
        $this->mailClass = app_path('Mail/TestMail.php');
        $this->mailView = resource_path('views/emails/test_mail.blade.php');

        File::delete([
            $this->mailClass, $this->mailView
        ]);

        $this->app['config']->set('varbox.emails.types', [
            $this->mailType => [
                'class' => 'App\Mail\TestMail',
                'view' => 'emails.test_mail',
                'variables' => [
                    'first_name', 'last_name', 'full_name'
                ],
            ]
        ]);
    }

    /** @test */
    public function it_creates_the_necessary_mailable_files()
    {
        $this->artisan('varbox:make-mail', ['type' => $this->mailType, '--no-interaction' => true])
            ->expectsOutput('Mailable created successfully!')
            ->assertExitCode(0);

        File::exists(app_path('Mail/TestMail.php'));
        File::exists(resource_path('views/emails/test_mail.blade.php'));
    }

    /** @test */
    public function it_fails_if_the_mailable_for_the_specified_type_already_exists()
    {
        $this->artisan('varbox:make-mail', ['type' => $this->mailType, '--no-interaction' => true])
            ->expectsOutput('Mailable created successfully!')
            ->assertExitCode(0);

        $this->artisan('varbox:make-mail', ['type' => $this->mailType, '--no-interaction' => true])
            ->expectsOutput('The mailable class for the "' . $this->mailType . '" email type already exists!')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_fails_if_the_mailable_type_doesnt_exist_inside_the_emails_config()
    {
        $this->artisan('varbox:make-mail', ['type' => 'test-type', '--no-interaction' => true])
            ->expectsOutput('There is no email type called "test-type".')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_asks_if_the_generated_mailable_should_support_queueing()
    {
        $this->artisan('varbox:make-mail', ['type' => $this->mailType])
            ->expectsQuestion('Do you want to make the mail queueable?', 'no')
            ->expectsOutput('Mailable created successfully!')
            ->assertExitCode(0);
    }

    /** @test */
    public function itcan__generate_the_mailable_class_without_queue_support()
    {
        $this->artisan('varbox:make-mail', ['type' => $this->mailType])
            ->expectsQuestion('Do you want to make the mail queueable?', 'no')
            ->expectsOutput('Mailable created successfully!')
            ->assertExitCode(0);

        $contents = File::get($this->mailClass);

        $this->assertFalse(Str::contains($contents, 'implements ShouldQueue'));
        $this->assertFalse(Str::contains($contents, 'Queueable,'));
    }

    /** @test */
    public function it_can_generate_the_mailable_class_with_queue_support()
    {
        $this->artisan('varbox:make-mail', ['type' => $this->mailType])
            ->expectsQuestion('Do you want to make the mail queueable?', 'yes')
            ->expectsOutput('Mailable created successfully!')
            ->assertExitCode(0);

        $contents = File::get($this->mailClass);

        $this->assertTrue(Str::contains($contents, 'implements ShouldQueue'));
        $this->assertTrue(Str::contains($contents, 'Queueable,'));
    }
}
