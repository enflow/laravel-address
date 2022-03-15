<?php

namespace Enflow\Address\Drivers;

use Enflow\Address\Exceptions\ConfigurationException;
use Enflow\Address\Models\Address;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HereDriver extends Driver
{
    protected string $endpoint = 'autosuggest.search.hereapi.com';

    public function __construct(protected ?string $token)
    {
        if (empty($this->token)) {
            throw ConfigurationException::missingToken('here');
        }
    }

    public function suggest(array $options): Collection
    {
        // https://developer.here.com/documentation/geocoding-search-api/api-reference-swagger.html

        $response = Http::withOptions([
            // Here's API encoding seems broken: "cURL error 61: Error while processing content unencoding: incorrect header check"
            'decode_content' => false,
        ])->get('https://' . config('address.hero.endpoints.autosuggest', 'autosuggest.search.hereapi.com') . '/v1/geocode', [
            'q' => $options['query'],
            'in' => $options['filter'] ?? config('address.here.defaults.filter'),
            'resultType' => $options['resultType'] ?? config('address.here.defaults.result_type'),
            'limit' => $options['limit'] ?? 5,
            'lang' => app()->getLocale(),
            'apiKey' => $this->token,
        ]);

        return collect($response['items'] ?? null)
            ->map(fn ($item) => $this->suggestionToAddress($item))
            ->unique(fn (Address $address) => $address->label);
    }

    public function search(string $query, array $options = []): ?Address
    {
        return $this->suggest(['query' => $query, 'limit' => 1] + $options)->first();
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
