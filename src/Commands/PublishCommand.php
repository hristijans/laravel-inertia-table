<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class PublishCommand extends Command
{
    public $signature = 'inertia-table:install 
                        {--force : Overwrite any existing files}
                        {--only= : Only publish specific assets (config, migrations, assets)}';

    public $description = 'Install the Laravel Inertia Table package';

    public function handle(): int
    {
        $this->comment('Publishing Laravel Inertia Table Package...');

        $only = $this->option('only');

        if (! $only || $only === 'config') {
            $this->publishConfig();
        }

        if (! $only || $only === 'migrations') {
            $this->publishMigrations();
        }

        if (! $only || $only === 'assets') {
            $this->publishAssets();
            $this->updateJavaScriptConfig();
        }

        $this->info('Laravel Inertia Table installed successfully!');
        $this->newLine();

        if (! $only || $only === 'assets') {
            $this->comment('Please follow these steps to complete the installation:');
            $this->comment('1. Run npm install');
            $this->comment('2. Import the components in your JavaScript:');
            $this->newLine();
            $this->info('// Import in your app.js or similar file:');
            $this->info("import { InertiaTable } from './vendor/laravel-inertia-table';");
            $this->newLine();
            $this->comment('3. Use the component in your Inertia pages:');
            $this->info('<InertiaTable :table="$page.props.table" />');
        }

        return self::SUCCESS;
    }

    protected function publishConfig(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'Hristijans\\LaravelInertiaTable\\LaravelInertiaTableServiceProvider',
            '--tag' => 'laravel-inertia-table-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('✓ Configuration file published');
    }

    protected function publishMigrations(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'Hristijans\\LaravelInertiaTable\\LaravelInertiaTableServiceProvider',
            '--tag' => 'laravel-inertia-table-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->info('✓ Migration files published');
    }

    protected function publishAssets(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'Hristijans\\LaravelInertiaTable\\LaravelInertiaTableServiceProvider',
            '--tag' => 'laravel-inertia-table-assets',
            '--force' => $this->option('force'),
        ]);

        $this->info('✓ Vue components published to resources/js/vendor/laravel-inertia-table');
    }

    protected function updateJavaScriptConfig(): void
    {
        // Detect if using Vite or Laravel Mix
        if (File::exists(base_path('vite.config.js'))) {
            $this->updateViteConfig();
        } elseif (File::exists(base_path('webpack.mix.js'))) {
            $this->updateLaravelMixConfig();
        } else {
            $this->warn('Could not detect JavaScript build configuration (vite.config.js or webpack.mix.js).');
            $this->warn('You will need to manually update your build configuration to include the component assets.');
        }
    }

    protected function updateViteConfig(): void
    {
        $viteConfigPath = base_path('vite.config.js');
        $viteConfig = File::get($viteConfigPath);

        if (strpos($viteConfig, 'laravel-inertia-table') === false) {
            $this->info('Updating Vite configuration to include Inertia Table components...');

            // This is a simple update and might need manual adjustment
            $updatedConfig = str_replace(
                'input: [',
                "input: [\n            'resources/js/vendor/laravel-inertia-table/index.js',",
                $viteConfig
            );

            if ($this->option('force')) {
                File::put($viteConfigPath, $updatedConfig);
                $this->info('✓ Vite configuration updated');
            } else {
                $this->info('Please add the following to your Vite input files:');
                $this->info("'resources/js/vendor/laravel-inertia-table/index.js'");
            }
        } else {
            $this->info('✓ Vite configuration already includes Inertia Table components');
        }
    }

    protected function updateLaravelMixConfig(): void
    {
        $mixConfigPath = base_path('webpack.mix.js');
        $mixConfig = File::get($mixConfigPath);

        if (strpos($mixConfig, 'laravel-inertia-table') === false) {
            $this->info('Please add the following to your webpack.mix.js file:');
            $this->info(".js('resources/js/vendor/laravel-inertia-table/index.js', 'public/js')");
        } else {
            $this->info('✓ Laravel Mix configuration already includes Inertia Table components');
        }
    }
}
