<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor' => $this->whenLoaded('doctor', fn () => ['id' => $this->doctor->id, 'name' => $this->doctor->user?->name]),
            'patient' => $this->whenLoaded('patient', fn () => ['id' => $this->patient->id, 'name' => $this->patient->user?->name]),
            'appointment_id' => $this->appointment_id,
            'prescription_date' => $this->prescription_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id, 'medicine_name' => $item->medicine_name, 'dosage' => $item->dosage,
                'frequency' => $item->frequency, 'duration' => $item->duration, 'instructions' => $item->instructions,
            ])),
        ];
    }
}
