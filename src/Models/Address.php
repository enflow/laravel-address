<?php

namespace Enflow\Address\Models;

use Enflow\Address\AddressValueTransformer;
use Enflow\Address\Jobs\LocalizeAddressJob;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rinvex\Cacheable\CacheableEloquent;
use Spatie\Translatable\HasTranslations;
use Symfony\Component\Intl\Countries;

class Address extends Model
{
    use CacheableEloquent;
    use HasTranslations;
    protected $fillable = [
        'driver',
        'identifier',
        'label',
        'street',
        'house_number',
        'city',
        'postal_code',
        'state',
        'county',
        'country',
        'lat',
        'lng',
    ];
    protected $translatable = [
        'label',
        'state',
        'county',
        'city',
        'street',
    ];

    public function translatableFill(string $language, array $input)
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $this->translatable)) {
                $this->setTranslation($key, $language, $value);
            } elseif ($this->isFillable($key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function toAppLocalizedArray()
    {
        return [
            'driver' => $this->driver,
            'identifier' => $this->identifier,
            'label' => $this->label,
            'street' => $this->street,
            'house_number' => $this->house_number,
            'postal_code' => $this->postal_code,
            'state' => $this->state,
            'county' => $this->county,
            'city' => $this->city,
            'country' => $this->country,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    public function setCountryAttribute($country)
    {
        $country = $country['alpha2'] ?? $country;

        if (strlen($country) === 3) {
            $country = Countries::getAlpha2Code($country);
        }

        if (!Countries::exists($country)) {
            throw new Exception("Invalid country passed to CountryCast: {$country}");
        }

        $this->attributes['country'] = $country;
    }

    public function getCountryAttribute($country)
    {
        return [
            'name' => Countries::getName($country, app()->getLocale()),
            'alpha2' => $country,
            'alpha3' => Countries::getAlpha3Code($country),
        ];
    }

    public function value()
    {
        return $this->toAppLocalizedArray() + [
                'language' => app()->getLocale(),
                'hmac' => AddressValueTransformer::sign($this),
            ];
    }

    public static function persist($value): ?Address
    {
        if (empty($value)) {
            return null;
        }

        $address = AddressValueTransformer::decode($value);
        if (!$address) {
            throw new Exception("Invalid address passed. Should be handelled by the 'IsValidAddress' validation rule.");
        }

        return $address->findOrSave();
    }

    protected function findOrSave(): self
    {
        if ($address = static::where([
            'driver' => $this->driver,
            'identifier' => $this->identifier,
        ])->first()) {
            return $address;
        }

        $this->save();

        foreach (array_diff(config('address.locales', []), [app()->getLocale()]) as $locale) {
            LocalizeAddressJob::dispatch($this, $locale)->onQueue(config('address.localize_queue', null));
        }

        return $this;
    }
}
