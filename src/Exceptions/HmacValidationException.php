<?php

namespace Enflow\Address\Exceptions;

use Exception;

class HmacValidationException extends Exception implements AddressException
{
    public static function required()
    {
        return new static("Hmac was not available on object. Address could not be validated.");
    }

    public static function failed()
    {
        return new static("Hmac was available but signed incorrect.");
    }
}
