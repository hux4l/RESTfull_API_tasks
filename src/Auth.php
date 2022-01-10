<?php
class Auth
{
    private int $user_id;
    public function __construct(private UserGateway $user_gateway)
    {
    }

    public function authenticateAPIKey(): bool
    {
        
        // check if api-key is sended
        if (empty($_SERVER["HTTP_X_API_KEY"])) {

            http_response_code(400);
            echo json_encode(["message" => "missing API key"]);
            return false;
        }

        // get api-key from server header
        $api_key = $_SERVER["HTTP_X_API_KEY"];

        // retrieve user
        $user = $this->user_gateway->getByAPIKey($api_key) ;

        if ($user === false) {

            http_response_code(401);
            echo json_encode(["message" => "invalid API key"]);
            return false;        
        }

        // store user id
        $this->user_id = $user["id"];

        return true;
    }

    public function getUserID(): int
    {
        return $this->user_id;
    }

    // authenticate access token
    public function authenticateAccessToken(): bool
    {
        if (! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches))
        {
            http_response_code(400);
            echo json_encode(["message" => "incomplete authorization header"]);
            return false;
        }

        // decode text
        $plain_text = base64_decode($matches[1], true);

        // if access token not match return false
        if ($plain_text === false) {

            http_response_code(400);
            echo json_encode(["message" => "invalid authorization header"]);
            return false;
        }

        // if json is invalid will return null
        $data = json_decode($plain_text, true);

        if ($data === null) {

            http_response_code(400);
            echo json_encode(["message" => "invalid JSON"]);
            return false;
        }

        // get user id
        $this->user_id = $data["id"];

        // if everything is ok return true
        return true;
    }
}