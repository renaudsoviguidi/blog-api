<?php

namespace App\Http\Requests;

use App\Enums\CommentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ModerateCommentRequest extends FormRequest
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
            "status" => ["required", new Enum(CommentStatusEnum::class)],

            // Motif obligatoire seulement pour rejected
            "rejection_reason" => [
                "nullable",
                "string",
                "min:10",
                "max:500",
                "required_if:status," . CommentStatusEnum::Rejected->value,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "status.required" => "Le statut est obligatoire.",
            "rejection_reason.required_if" => "Le motif est obligatoire lors d'un rejet.",
            "rejection_reason.min" => "Le motif doit contenir au moins 10 caractères.",
        ];
    }
}
