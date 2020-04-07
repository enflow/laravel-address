<?php

namespace Enflow\Address\Drivers;

use Enflow\Address\Models\Address;
use Illuminate\Support\Collection;

abstract class Driver
{
    abstract public function suggest(array $options): Collection;
    abstract public function lookup(string $identifier, array $options = []): Address;
}
