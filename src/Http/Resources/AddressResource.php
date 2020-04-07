<?php

namespace Enflow\Address\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource->value();
    }
}
