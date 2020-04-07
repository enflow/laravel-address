<?php

namespace Enflow\Address\Test;

use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Enflow\Address\AddressServiceProvider::class,
        ];
    }
}
