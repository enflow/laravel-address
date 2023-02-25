<?php

namespace Enflow\Address\Test;

use Enflow\Address\Exceptions\CannotFindAddressException;
use Enflow\Address\Models\Address;
use Illuminate\Support\Facades\Http;

class HereDriverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('address.here.token', 'fake-token');
    }

    public function test_here_suggest_endpoint()
    {
        Http::fake([
            'autosuggest.search.hereapi.com/*' => Http::response(json_decode(file_get_contents(__DIR__.'/fixtures/here-suggest.json'), true)),
        ]);

        $response = $this->call('GET', route('address::suggest'), [
            'query' => 'Wilhelminalaan 2, Alphen aan den Rijn',
        ]);
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'driver' => 'here',
                        'identifier' => 'here:af:streetsection:dA.5Abx8yLpWIcSbvSRzbC:CggIBCDTzuukAhABGgEyKGQ',
                        'label' => 'Wilhelminalaan 2, 2405 ED Alphen aan den Rijn, Nederland',
                        'street' => 'Wilhelminalaan',
                        'house_number' => '2',
                        'postal_code' => '2405 ED',
                        'state' => 'Zuid-Holland',
                        'county' => 'Alphen aan den Rijn',
                        'city' => 'Alphen aan den Rijn',
                        'country' => [
                            'name' => 'Netherlands',
                            'alpha2' => 'NL',
                            'alpha3' => 'NLD',
                        ],
                        'lat' => 52.13059,
                        'lng' => 4.6622,
                        'hmac' => '059819e80a6ccafcb2ec9c6aee5dc1f6b80dfbd382096f309f8798c6759b3b9f',
                    ],
                ],
            ]);
    }

    public function test_here_address_create_from_string()
    {
        Http::fake([
            'autosuggest.search.hereapi.com/*' => Http::response(json_decode(file_get_contents(__DIR__.'/fixtures/here-suggest.json'), true)),
        ]);

        $address = Address::createFromSearch('Wilhelminalaan 2, Alphen aan den Rijn');

        $this->assertDatabaseHas('addresses', ['id' => $address->id]);

        $this->assertNotEmpty($address->lat);
        $this->assertNotEmpty($address->lng);
    }

    public function test_here_address_create_from_string_throws_exception_on_no_results()
    {
        Http::fake([
            'autosuggest.search.hereapi.com/*' => Http::response(json_decode(file_get_contents(__DIR__.'/fixtures/here-suggest-empty.json'), true)),
        ]);

        $this->expectException(CannotFindAddressException::class);
        Address::createFromSearch('Middle of nowhere, Nowhere 300');
    }
}
