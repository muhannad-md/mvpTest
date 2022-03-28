<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => (string)$this->id,
            'attributes' => [
                'name' => $this->name,
                'amount' => $this->amount,
                'cost' => $this->cost,
                'user' => [
                    'id' => $this->user->id,
                    'username' => $this->user->username,
                ],
            ],
        ];
    }
}
