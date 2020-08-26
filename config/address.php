<?php

return [
    'driver' => 'here',

    'here' => [
        'token' => env('HERE_TOKEN', ''),
    ],

    'algolia_places' => [
        'app_id' => env('ALGOLIA_PLACES_APP_ID', 'plVFM0AS0MOQ'),
        'api_key' => env('ALGOLIA_PLACES_API_KEY', 'c2b67bde0503fab6cc3ecd3e3302323e'),
    ],

    'locales' => null, // By default app.locale
];
