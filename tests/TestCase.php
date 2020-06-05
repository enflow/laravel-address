<?php

namespace Enflow\Address\Test;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');

        $this->app['config']->set('database.default', 'testbench');
        $this->app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));

        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Enflow\Address\AddressServiceProvider::class,
        ];
    }
}
