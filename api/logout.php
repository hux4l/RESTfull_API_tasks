<?php

// get refresh token if exists store, else error
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

// if other method as POST return allowed only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);
    header("Allow: POST");
    exit;
}

// encode url data as json
$data = (array) json_decode(file_get_contents("php://input"), true);

// if username or password are not given return bad request
if ( ! array_key_exists("token", $data)) {

    http_response_code(400);
    echo json_encode(["message" => "missing login credentials"]);
    exit;
}

// encode token
$codec = new JWTCodec($_ENV["SECRET_KEY"]);

try {
    $payload = $codec->decode($data["token"]);
} catch (Exception) {

    http_response_code(400);
    echo json_encode(["message" => "invalid token"]);
    exit;
}

$database = new Database($_ENV["DB_HOST"],
                                            $_ENV["DB_NAME"],
                                            $_ENV["DB_USER"],
                                            $_ENV["DB_PASS"]);

$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);

$refresh_token_gateway->delete($data["token"]);