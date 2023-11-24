<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientMedicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $frequency = '';
        switch ($this->reminder->dosage_frequency) {
            case 'ONCE_DAILY':
                $frequency = 'Once Everyday';
                break;
            case 'TWICE_DAILY':
                $frequency = 'Twice Everyday';
                break;
            case 'THRICE_DAILY':
                $frequency = 'Three Times Everyday';
                break;
        }
        return [
            'drug_name' => $this->name,
            'dosage' => $this->dosage,
            'status' => $this->status,
            'dosage_frequency' => $frequency,
            'next_reminder' => date('Y-m-d H:i:s', $this->reminder->next_reminder_at),
            'start_date' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
