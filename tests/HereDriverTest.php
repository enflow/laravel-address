<?php

namespace Enflow\Address\Test;

use Illuminate\Support\Facades\Http;

class HereDriverTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('address', [
            'driver' => 'here',
            'here' => [
                'token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ]
        ]);
    }

    public function test_here_suggest_endpoint()
    {
        app()->setLocale('en');

        Http::fake([
            'autosuggest.search.hereapi.com/*' => Http::response(json_decode(file_get_contents(__DIR__ . '/fixtures/here-suggest.json'), true)),
        ]);

        $response = $this->call('GET', route('address::suggest'), [
            'query' => 'Wilhelminalaan 2, Alphen aan den Rijn',
         ]);
        $response
            ->assertStatus(200)
            ->assertExactJson(array(
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
                        'country' =>
                            array(
                                'name' => 'Netherlands',
                                'alpha2' => 'NL',
                                'alpha3' => 'NLD',
                            ),
                        'lat' => 52.13059,
                        'lng' => 4.6622,
                        'hmac' => '059819e80a6ccafcb2ec9c6aee5dc1f6b80dfbd382096f309f8798c6759b3b9f',
                    ],
                ],
            ));
    }
}
