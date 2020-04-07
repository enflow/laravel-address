<?php

namespace Enflow\Address\Http\Resources;

use Enflow\Address\AddressValueTransformer;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource->value();
    }
}
