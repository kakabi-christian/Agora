<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'membre' => new MembreResource($this->resource['membre']),
            'token' => $this->resource['token'],
            'token_type' => 'Bearer',
            'expires_in' => 7200, // 2 hours in seconds
        ];
    }
}
