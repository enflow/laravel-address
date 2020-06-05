<?php

namespace Enflow\Address\Exceptions;

use Exception;

class CannotFindAddressException extends Exception implements AddressException
{
    public static function searchNoResults(string $query)
    {
        return new static("Cannot find address by searching '{$query}'");
    }
}
