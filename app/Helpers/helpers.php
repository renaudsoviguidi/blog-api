<?php
if (!function_exists('apiResponse')) {

    function apiResponse(bool $success, string $message, $data = null, int $status = 200)
    {
        return response()->json([
            "success" => $success,
            "message" => $message,
            "data" => $data
        ], $status);
    }

}