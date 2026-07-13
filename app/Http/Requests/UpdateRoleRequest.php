<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'libelle' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'libelle')->ignore($this->route('role')?->id),
            ],
            'description' => 'nullable|string|max:255',
            'habilitation_ids' => 'nullable|array',
            'habilitation_ids.*' => 'exists:habilitations,id',
        ];
    }
}
