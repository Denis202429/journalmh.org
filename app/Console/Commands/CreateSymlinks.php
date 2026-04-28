<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateSymlinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:create-symlinks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create symbolic links for storage and public.html';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating symbolic links...');

        // Create the storage link
        if (!File::exists(public_path('storage'))) {
            $this->call('storage:link');
        } else {
            $this->info('The [public/storage] link already exists.');
        }

        // Create the public.html link
        if (!File::exists(public_path('public.html'))) {
            symlink(public_path(), public_path('public.html'));
            $this->info('The [public.html] link has been connected to [public].');
        } else {
            $this->info('The [public.html] link already exists.');
        }

        return Command::SUCCESS;
    }
}

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ   ӑӗҫӳ ӐӖҪӲ

