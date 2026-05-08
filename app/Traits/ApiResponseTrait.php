<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function success(mixed $data = null, string $message = "Succès", int $status = 200)
    {
        $response = [
            "success" => true,
            "message" => $message,
        ];

        // On n'inclut "data" que si on a quelque chose à retourner
        if (!is_null($data)) {
            $response["data"] = $data;
        }

        return response()->json($response, $status);
    }

    protected function error(string $message = "Erreur", mixed $errors = null, int $status = 400)
    {
        $response = [
            "success" => false,
            "message" => $message,
        ];

        // On n'inclut "errors" que si on a des détails à retourner
        if (!is_null($errors)) {
            $response["errors"] = $errors;
        }

        return response()->json($response, $status);
    }
}