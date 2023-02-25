<?php

namespace Enflow\Address;

use Enflow\Address\Drivers\Driver;
use Illuminate\Support\Manager;

class DriverManager extends Manager
{
    public function driver($name = null): Driver
    {
        return parent::driver($name);
    }

    public function createHereDriver(): Driver
    {
        return new Drivers\HereDriver(
            config('address.here.token'),
        );
    }

    public function getDefaultDriver()
    {
        return $this->container['config']['address.driver'];
    }
}
