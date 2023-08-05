<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'co_usuario' => $this->resource->co_usuario,
            'no_usuario' => $this->resource->no_usuario,
        ];
    }
}
