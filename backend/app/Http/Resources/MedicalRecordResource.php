<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor' => $this->whenLoaded('doctor', fn () => ['id' => $this->doctor->id, 'name' => $this->doctor->user?->name]),
            'patient' => $this->whenLoaded('patient', fn () => ['id' => $this->patient->id, 'name' => $this->patient->user?->name]),
            'appointment_id' => $this->appointment_id,
            'date' => $this->appointment?->appointment_date?->format('Y-m-d'),
            'diagnosis' => $this->diagnosis,
            'symptoms' => $this->symptoms,
            'treatment' => $this->treatment,
            'notes' => $this->notes,
        ];
    }
}
