<?php

namespace Enflow\Address\Models;

use Enflow\Address\AddressValueTransformer;
use Enflow\Address\DriverManager;
use Enflow\Address\Exceptions\CannotFindAddressException;
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

    public const DISTANCEKM = 111.1;

    public function value()
    {
        $values = [
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

        return $values + [
            'hmac' => AddressValueTransformer::sign($values),
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

    public static function createFromSearch(string $query): ?self
    {
        $address = app(DriverManager::class)->driver()->search($query);

        if (!$address) {
            throw CannotFindAddressException::searchNoResults($query);
        }

        return $address->findOrSave();
    }

    public static function persist($value): ?self
    {
        if (empty($value)) {
            return null;
        }

        $address = AddressValueTransformer::decode($value);

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

        return $this;
    }
    
    public function getDistance($address = null)
    {
        if (!$address || !($address->lat ?? null) || !($address->lng ?? null)) {
            return 0;
        }

        $latDiff = ($this->lat - $address->lat) * self::DISTANCEKM;
        $lngDiff = ($this->lng - $address->lng) * self::DISTANCEKM;

        return round($this->pythagoras($latDiff, $lngDiff), 3);
    }

    private function pythagoras(float $x, float $y)
    {
        return sqrt(($x**2) + ($y**2));
    }
}
