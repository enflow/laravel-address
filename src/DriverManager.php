<?php

namespace Enflow\Address;

use Illuminate\Support\Manager;
use Enflow\Address\Drivers\Driver;

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
