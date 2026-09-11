<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PatientResource
 * Formats patient data for API responses.
 * Never exposes: password, remember_token, internal tokens.
 */
class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,

            // User account fields (from relationship)
            'name'              => $this->user?->name,
            'email'             => $this->user?->email,
            'phone'             => $this->user?->phone,

            // Patient-specific profile
            'date_of_birth'     => $this->date_of_birth?->toDateString(),
            'gender'            => $this->gender,
            'address'           => $this->address,
            'emergency_contact' => $this->emergency_contact,

            'created_at'        => $this->created_at?->toDateTimeString(),
            'updated_at'        => $this->updated_at?->toDateTimeString(),
        ];
    }
}
