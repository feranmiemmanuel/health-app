<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientsDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'patient_id' => $this->id,
            'patient_token' => $this->patient_id,
            'name' => $this->user->first_name . ' ' . $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ];
    }
}
