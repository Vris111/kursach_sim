<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this -> id,
            'name' => $this -> name,
            'country' => $this -> country,
            'description' => $this -> description,
            'starting_date' => $this -> starting_date,
            'days_count' => $this -> days_count,
            'peoples_count' => $this -> peoples_count,
            'price' => $this -> price,
            'img' => $this-> img,
        ];
    }
}
