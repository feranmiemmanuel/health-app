<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientReminderHistoryResource extends JsonResource
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
            'history_id' => $this->id,
            'status' => $this->status,
            'reminder_id' => $this->reminder->id,
            'drug' => $this->reminder->medication->name,
            'dosage' => $this->reminder->medication->dosage,
            'dosage_frequency' => $frequency,
            'reminded_at' => date('Y-m-d H:i:s', $this->reminded_at),
            'following_reminder' => date('Y-m-d H:i:s', $this->reminder->next_reminder_at),
        ];
    }
}
