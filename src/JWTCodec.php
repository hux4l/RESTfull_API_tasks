<?php

class JWTCodec
{
    // function to encode jwt
    public function encode(array $payload): string
    {
        $header = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);

        // encode header
        $header = $this->base64urlEncode($header);

        // encode payload
        $payload = json_encode($payload);
        $payload = $this->base64urlEncode($payload);

        $signature = hash_hmac("sha256",
                                                $header . "." . $payload,
                                                "2D4B6150645367566B58703273357638792F423F4528482B4D6251655468576D",
                                                true);

        $signature =  $this->base64urlEncode($signature);

        return $header . "." . $payload . "." . $signature;

    }

    // encode url
    private function base64urlEncode(string $text): string
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($text)
        );
    }

    // generated encryption key 2D4B6150645367566B58703273357638792F423F4528482B4D6251655468576D

}