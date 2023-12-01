<?php

namespace App\Traits;




trait ApiHttpResponses
{

    public function sendResponse($results, $message = "Successfully", $status = 200)
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            "data" => $results
        ], $status);
    }
    public function sendErrors($errors, $message = "Not found", $status = 404)
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "data" => $errors
        ], $status);
    }
}

