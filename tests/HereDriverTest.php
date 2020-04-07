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
        Http::fake([
            'autosuggest.search.hereapi.com/*' => Http::response(json_decode(file_get_contents(__DIR__ . '/fixtures/here-suggest.json'), true)),
        ]);

        $response = $this->call('GET', route('address::suggest'), ['query' => 'Wilhelminalaan 2, Alphen aan den Rijn']);
        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.driver', 'here')
            ->assertJsonPath('data.0.country.alpha2', 'NL');
    }
}
