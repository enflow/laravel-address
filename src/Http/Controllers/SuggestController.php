<?php

namespace Enflow\Address\Http\Controllers;

use Enflow\Address\DriverManager;
use Enflow\Address\Http\Resources\AddressResource;
use Enflow\Address\Models\Address;
use Illuminate\Http\Request;

class SuggestController
{
    public function __invoke(DriverManager $manager, Request $request)
    {
        $options = $request->validate([
            'query' => 'string',
            'limit' => 'integer|min:1|max:100',
        ]);

        if (empty($request->get('query'))) {
            return [];
        }

        return AddressResource::collection($manager->driver()->suggest($options));
    }
}
