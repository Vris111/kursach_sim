<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        switch ($this->status) {
            case 'одобрена':
                $statusMessage = 'Ваша заявка одобрена. Пожалуйста, приходите в офис для оплаты и получения документов.';
                break;
            default:
                $statusMessage = $this->status;
                break;
        }
        return [
            'id' => $this->id,
            'user' => $this->user->name,
            'tour' => $this->tour->name,
            'status' => $statusMessage
        ];
    }
}
