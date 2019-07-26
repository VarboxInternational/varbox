<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;

class FroalaLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:froala-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link for the froala storage disk from "public/froala/" to "storage/froala/"';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        if (file_exists(public_path('froala'))) {
            $this->error('The "public/froala/" directory already exists.');

            return false;
        }

        $this->laravel->make('files')->link(
            storage_path('froala'), public_path('froala')
        );

        $this->info('The "public/froala/" directory has been linked.');

        return true;
    }
}
