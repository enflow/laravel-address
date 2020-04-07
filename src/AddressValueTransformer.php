<?php

namespace Enflow\Address;

use Enflow\Address\Models\Address;

class AddressValueTransformer
{
    /**
     * @param string|array $value
     * @return Address|null
     */
    public static function decode($value): ?Address
    {
        $value = is_string($value) ? json_decode($value, true) : $value;

        if (empty($value['language']) || empty($value['hmac'])) {
            return null;
        }

        $resource = (new Address())->translatableFill($value['language'], $value);

        if (!hash_equals(static::sign($resource), $value['hmac'])) {
            return null;
        }

        return $resource;
    }

    public static function sign(Address $resource)
    {
        return hash_hmac('sha256', json_encode($resource->toAppLocalizedArray()), config('app.key'));
    }
}
