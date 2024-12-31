<?php
namespace Konnect\NayaFramework\Lib;

class JsonResponse {
    public static function send($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data); // Output the JSON response
        exit(); // Ensure the script terminates after sending the response
    }
}

