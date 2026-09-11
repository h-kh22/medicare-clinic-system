<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isAdmin() === true; }
    public function rules(): array
    {
        $specialty = $this->route('specialty');
        $id = is_object($specialty) ? $specialty->id : $specialty;
        return ['name' => ['sometimes', 'string', 'max:255', 'unique:specialties,name,'.$id], 'description' => ['sometimes', 'nullable', 'string']];
    }
}
