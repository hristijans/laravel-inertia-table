<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Commands;

use Illuminate\Console\Command;

final class PublishCommand extends Command
{
    public $signature = 'inertia-table:publish {--force : Overwrite any existing files}';

    public $description = 'Publish all assets and configuration for the laravel-inertia-table package';

    public function handle(): int
    {
        $this->comment('Publishing configuration...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Hristijans\\LaravelInertiaTable\\LaravelInertiaTableServiceProvider',
            '--tag' => 'laravel-inertia-table-config',
            '--force' => $this->option('force'),
        ]);

        $this->comment('Publishing assets...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Hristijans\\LaravelInertiaTable\\LaravelInertiaTableServiceProvider',
            '--tag' => 'laravel-inertia-table-assets',
            '--force' => $this->option('force'),
        ]);

        $this->comment('Publishing migrations...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Hristijans\\LaravelInertiaTable\\LaravelInertiaTableServiceProvider',
            '--tag' => 'laravel-inertia-table-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->info('All assets published successfully!');

        return self::SUCCESS;
    }
}
