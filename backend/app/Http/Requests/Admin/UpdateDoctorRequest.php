<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isAdmin() === true; }

    public function rules(): array
    {
        $doctor = $this->route('doctor');
        $doctorId = is_object($doctor) ? $doctor->id : $doctor;
        $userId = is_object($doctor) ? $doctor->user_id : null;
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'specialty_id' => ['sometimes', 'integer', 'exists:specialties,id'],
            'license_number' => ['sometimes', 'string', 'max:100', 'unique:doctors,license_number,'.$doctorId],
            'bio' => ['sometimes', 'nullable', 'string'],
            'consultation_fee' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
