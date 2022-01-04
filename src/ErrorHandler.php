<?php

class ErrorHandler
{
    public static function handleException(Throwable $exception): void
    {
        http_response_code(500);
        // return json with error message
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
}