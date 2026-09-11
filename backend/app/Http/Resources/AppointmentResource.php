<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor' => $this->whenLoaded('doctor', fn () => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->user?->name,
                'specialty' => $this->doctor->specialty?->name,
            ]),
            'patient' => $this->whenLoaded('patient', fn () => [
                'id' => $this->patient->id,
                'name' => $this->patient->user?->name,
                'email' => $this->patient->user?->email,
            ]),
            'appointment_date' => $this->appointment_date?->format('Y-m-d'),
            'appointment_time' => substr((string) $this->appointment_time, 0, 5),
            'status' => $this->status,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
