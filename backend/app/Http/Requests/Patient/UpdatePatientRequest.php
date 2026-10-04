<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdatePatientRequest
 * Validates patient profile update data.
 * All fields are optional on update (PATCH semantics).
 */
class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization is handled by the PatientPolicy — always return true here.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for updating a patient profile.
     */
    public function rules(): array
    {
        $patient = $this->route('patient');
        $userId = is_object($patient) ? $patient->user_id : $this->user()->id;

        return [
            // User account fields (name, email, phone)
            'name'              => ['sometimes', 'string', 'max:255'],
            'email'             => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'             => ['sometimes', 'nullable', 'string', 'max:20'],

            // Patient profile fields
            'date_of_birth'     => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender'            => ['sometimes', 'nullable', 'in:male,female,other'],
            'address'           => ['sometimes', 'nullable', 'string', 'max:500'],
            'emergency_contact' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom human-readable error messages.
     */
    public function messages(): array
    {
        return [
            'email.unique'          => 'This email is already taken by another account.',
            'date_of_birth.before'  => 'Date of birth must be in the past.',
            'gender.in'             => 'Gender must be one of: male, female, other.',
        ];
    }
}
