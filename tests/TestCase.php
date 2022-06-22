<?php

namespace East\LaravelActivityfeed\Tests;

use East\LaravelActivityfeed\LaravelActivityfeedServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\UnitTestCase;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setup() : void
    {
        parent::setUp();
/*        $this->withoutExceptionHandling();
        $this->artisan('migrate', ['--database' => 'testing']);

        $this->loadMigrationsFrom(__DIR__ . '/../src/Database/migrations');
        $this->loadLaravelMigrations(['--database' => 'testing']);

        $this->withFactories(__DIR__ . '/../src/Database/factories');*/
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [LaravelActivityfeedServiceProvider::class];
    }
}
