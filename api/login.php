<?php

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
if ( ! array_key_exists("username", $data) || ! array_key_exists("password", $data)) {

    http_response_code(400);
    echo json_encode(["message" => "missing login credentials"]);
    exit;
}

$database = new Database($_ENV["DB_HOST"],
                                            $_ENV["DB_NAME"],
                                            $_ENV["DB_USER"],
                                            $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);

$user = $user_gateway->getByUsername($data["username"]);

// check if user exists
if ($user === false) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

// check if password is valid
if ( ! password_verify($data["password"], $user["password_hash"])) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

// return access token
// in JWT need to be exactly needed names
// exp adding expire time to token, with 300 seconds
$payload = [
    "sub" => $user["id"],
    "name" => $user["name"],
    "exp" => time() + 300
];

$codec = new JWTCodec($_ENV["SECRET_KEY"]);
$access_token = $codec->encode($payload);

echo json_encode([ "access_token" => $access_token]);