<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Tests;

use Hristijans\LaravelInertiaTable\LaravelInertiaTableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelInertiaTableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_table_state_table.php';
        $migration->up();
    }
}
