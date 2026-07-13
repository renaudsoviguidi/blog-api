<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'libelle' => 'required|string|max:50|unique:roles,libelle',
            'description' => 'nullable|string|max:255',
            'habilitation_ids' => 'nullable|array',
            'habilitation_ids.*' => 'exists:habilitations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le nom du rôle est obligatoire.',
            'libelle.unique' => 'Ce rôle existe déjà.',
        ];
    }
}
