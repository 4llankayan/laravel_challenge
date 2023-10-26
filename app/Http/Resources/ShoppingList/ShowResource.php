<?php

namespace App\Http\Resources\ShoppingList;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bought_at' => $this->bought_at,
            'user_id' => $this->user->id,
            'products' => $this->products,
        ];
    }
}
