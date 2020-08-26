<?php

namespace Enflow\Address\Exceptions;

use Exception;

class ConfigurationException extends Exception implements AddressException
{
    public static function invalidFor(string $driver)
    {
        return new static("Missing requires credentials for '{$driver}'");
    }
}
