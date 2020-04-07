<?php

namespace Enflow\Address\Drivers;

use Enflow\Address\Models\Address;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HereDriver extends Driver
{
    protected string $token;
    protected string $endpoint = 'autosuggest.search.hereapi.com';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function suggest(array $options): Collection
    {
        // https://developer.here.com/documentation/geocoding-search-api/api-reference-swagger.html

        $response = Http::get('https://' . config('address.hero.endpoints.autosuggest', 'autosuggest.search.hereapi.com') . '/v1/geocode', [
            'q' => $options['query'],
            'limit' => $options['limit'] ?? 5,
            'lang' => app()->getLocale(),
            'apiKey' => $this->token,
        ]);

        return collect($response['items'] ?? null)
            ->map(function ($item) {
                return $this->suggestionToAddress($item);
            });
    }

    protected function suggestionToAddress(array $item): Address
    {
        return tap(new Address, function (Address $address) use ($item) {
            $address->fill([
                'driver' => 'here',
                'identifier' => Arr::get($item, 'id'),
                'label' => Arr::get($item, 'address.label'),
                'street' => Arr::get($item, 'address.street'),
                'house_number' => Arr::get($item, 'address.houseNumber'),
                'postal_code' => Arr::get($item, 'address.postalCode'),
                'state' => Arr::get($item, 'address.state'),
                'county' => Arr::get($item, 'address.county'),
                'city' => Arr::get($item, 'address.city'),
                'country' => Arr::get($item, 'address.countryCode'),
                'lat' => Arr::get($item, 'position.lat'),
                'lng' => Arr::get($item, 'position.lng'),
            ]);
        });
    }
}
