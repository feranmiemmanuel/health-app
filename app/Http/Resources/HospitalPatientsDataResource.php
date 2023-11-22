<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalPatientsDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'age' => $this->age,
            'date_of_birth' => $this->date_of_birth,
            'blood_group' => $this->blood_group,
            'genotype' => $this->genotype,
            'gender' => $this->gender,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'emergency_contact_email' => $this->emergency_contact_email,
            'allergies' => $this->allergies,
            'condition' => $this->condition,
        ];
    }
}
