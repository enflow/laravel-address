<?php

namespace Enflow\Address\Exceptions;

use Exception;

class ConfigurationException extends Exception implements AddressException
{
    public static function missingToken(string $driver): self
    {
        return new static("Missing token for driver '{$driver}'");
    }
}
