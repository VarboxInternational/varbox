<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;

class UploadsLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:uploads-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link for the uploads storage disk from "public/uploads/" to "storage/uploads/"';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        if (file_exists(public_path('uploads'))) {
            $this->error('The "public/uploads/" directory already exists.');

            return false;
        }

        $this->laravel->make('files')->link(
            storage_path('uploads'), public_path('uploads')
        );

        $this->info('The "public/uploads/" directory has been linked.');

        return true;
    }
}
