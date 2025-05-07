<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LaravelInertiaTableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-inertia-table')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasMigration('create_table_state_table')
            ->hasCommand(Commands\PublishCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Table::class, fn (): \Hristijans\LaravelInertiaTable\Table => new Table);
    }
}
