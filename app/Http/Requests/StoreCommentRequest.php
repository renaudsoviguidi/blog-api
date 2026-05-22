<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            "content" => "required|string|min:3|max:1000",
            "parent_id" => "nullable|exists:comments,id",
            // Champs invité — obligatoires si non connecté
            "guest_name" => "required_if:user_id,null|nullable|string|max:100",
            "guest_email" => "required_if:user_id,null|nullable|email|max:150",
        ];
    }

    public function messages(): array
    {
        return [
            "content.required" => "Le commentaire ne peut pas être vide.",
            "content.min" => "Le commentaire doit contenir au moins 3 caractères.",
            "content.max" => "Le commentaire ne peut pas dépasser 1000 caractères.",
            "guest_name.required_if" => "Le nom est requis pour les visiteurs.",
            "guest_email.required_if" => "L'email est requis pour les visiteurs.",
        ];
    }

}
