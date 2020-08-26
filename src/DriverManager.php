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

    public function createAlgoliaPlacesDriver(): Driver
    {
        return new Drivers\AlgoliaPlacesDriver(
            config('address.algolia_places.app_id'),
            config('address.algolia_places.api_key'),
        );
    }

    public function getDefaultDriver()
    {
        return $this->container['config']['address.driver'];
    }
}
