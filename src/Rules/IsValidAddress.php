<?php

namespace Enflow\Address\Rules;

use Enflow\Address\AddressValueTransformer;
use Enflow\Address\Exceptions\HmacValidationException;
use Enflow\Address\Models\Address;
use Illuminate\Contracts\Validation\Rule;

class IsValidAddress implements Rule
{
    public function passes($attribute, $value): bool
    {
        try {
            return AddressValueTransformer::decode($value) instanceof Address;
        } catch (HmacValidationException $e) {
            return false;
        }
    }

    public function message(): string
    {
        return trans('address::validation.address_is_invalid');
    }
}
