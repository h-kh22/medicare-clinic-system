<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:pending,confirmed,completed,cancelled'],
            'appointment_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'appointment_time' => ['sometimes', 'date_format:H:i'],
            'reason' => ['sometimes', 'string', 'max:2000'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
