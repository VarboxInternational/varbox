<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Exceptions\EmailException;
use Varbox\Models\Email;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasRevisions;
use Varbox\Traits\HasUploads;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsDraftable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class EmailTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var string
     */
    protected $type = 'test-type';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('varbox.emails.types', [
            $this->type => [
                'class' => 'App\Mail\TestMail',
                'view' => 'emails.test_mail',
                'variables' => [
                    'first_name', 'last_name', 'full_name'
                ],
            ]
        ]);
    }

    /** @test */
    public function it_uses_the_has_uploads_trait()
    {
        $this->assertArrayHasKey(HasUploads::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_has_revisions_trait()
    {
        $this->assertArrayHasKey(HasRevisions::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_has_duplicates_trait()
    {
        $this->assertArrayHasKey(HasDuplicates::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_is_draftable_trait()
    {
        $this->assertArrayHasKey(IsDraftable::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Email::class));
    }

    /** @test */
    public function it_uses_the_soft_deletes_trait()
    {
        $this->assertArrayHasKey(SoftDeletes::class, class_uses(Email::class));
    }

    /** @test */
    public function it_can_return_the_from_address_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('Test From Email', $this->email->from_address);
    }

    /** @test */
    public function it_returns_the_from_address_from_config_when_no_value_on_email_model()
    {
        $this->createEmail();

        $this->email->update([
            'data' => ['from_email' => null]
        ]);

        $this->app['config']->set('mail.from.address', 'hello@example.com');

        $this->assertEquals('hello@example.com', $this->email->from_address);
    }

    /** @test */
    public function it_can_return_the_from_name_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('Test From Name', $this->email->from_name);
    }

    /** @test */
    public function it_returns_the_from_name_from_config_when_no_value_on_email_model()
    {
        $this->createEmail();

        $this->email->update([
            'data' => ['from_name' => null]
        ]);

        $this->app['config']->set('mail.from.name', 'Example');

        $this->assertEquals('Example', $this->email->from_name);
    }

    /** @test */
    public function it_can_return_the_reply_to_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('Test Reply To', $this->email->reply_to);
    }

    /** @test */
    public function it_returns_the_reply_to_from_config_when_no_value_on_email_model()
    {
        $this->createEmail();

        $this->email->update([
            'data' => ['reply_to' => null]
        ]);

        $this->app['config']->set('mail.from.address', 'hello@example.com');

        $this->assertEquals('hello@example.com', $this->email->reply_to);
    }

    /** @test */
    public function it_can_return_the_subject_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('Test Subject', $this->email->subject);
    }

    /** @test */
    public function it_can_return_the_message_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('Test Message', $this->email->message);
    }

    /** @test */
    public function it_can_return_the_attachment_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('Test Attachment', $this->email->attachment);
    }

    /** @test */
    public function it_can_return_the_view_of_an_email_record()
    {
        $this->createEmail();

        $this->assertEquals('emails.test_mail', $this->email->view);
    }

    /** @test */
    public function it_can_return_the_variables_of_an_email_record()
    {
        $this->createEmail();

        $variables = $this->email->variables;

        $this->assertArrayHasKey('first_name', $variables);
        $this->assertArrayHasKey('last_name', $variables);
        $this->assertArrayHasKey('full_name', $variables);

        $this->assertEquals('First Name', $variables['first_name']['name']);
        $this->assertEquals('Last Name', $variables['last_name']['name']);
        $this->assertEquals('Full Name', $variables['full_name']['name']);

        $this->assertEquals('emails.test_mail', $this->email->view);
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        Email::create(['name' => 'Some email', 'type' => 'test-email']);
        Email::create(['name' => 'Another email', 'type' => 'test-email']);
        Email::create(['name' => 'The email', 'type' => 'test-email']);

        $this->assertEquals('Another email', Email::alphabetically()->get()->first()->name);
        $this->assertEquals('The email', Email::alphabetically()->get()->last()->name);
    }

    /** @test */
    public function it_can_get_an_email_instance_by_type()
    {
        $this->createEmail();

        $email = Email::findByType($this->type);

        $this->assertEquals('Test Email', $email->name);
        $this->assertEquals($this->type, $email->type);

        $this->expectException(EmailException::class);

        Email::findByType('no-type');
    }

    /**
     * @return void
     */
    protected function createEmail()
    {
        $this->email = Email::create([
            'name' => 'Test Email',
            'type' => $this->type,
            'data' => [
                'subject' => 'Test Subject',
                'message' => 'Test Message',
                'attachment' => 'Test Attachment',
                'from_name' => 'Test From Name',
                'from_email' => 'Test From Email',
                'reply_to' => 'Test Reply To',
            ],
        ]);
    }
}
