<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;

class WysiwygLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:wysiwyg-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link for the wysiwyg storage disk from "public/wysiwyg/" to "storage/wysiwyg/"';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        if (file_exists(public_path('wysiwyg'))) {
            $this->error('The "public/wysiwyg/" directory already exists.');

            return false;
        }

        $this->laravel->make('files')->link(
            storage_path('wysiwyg'), public_path('wysiwyg')
        );

        $this->info('The "public/wysiwyg/" directory has been linked.');

        return true;
    }
}
