<?php

namespace Enflow\Address;

use Enflow\Address\Exceptions\HmacValidationException;
use Enflow\Address\Models\Address;
use Illuminate\Support\Arr;

class AddressValueTransformer
{
    public static function decode($value): Address
    {
        $value = is_string($value) ? json_decode($value, true) : $value;

        if (empty($value['hmac'])) {
            throw HmacValidationException::required();
        }

        if (!hash_equals(static::sign(Arr::except($value, 'hmac')), $value['hmac'])) {
            throw HmacValidationException::failed();
        }

        return new Address($value);
    }

    public static function sign(array $value)
    {
        return hash_hmac('sha256', json_encode($value), config('app.key'));
    }
}
