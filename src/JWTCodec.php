<?php

class JWTCodec
{

    // add encryption key as an argument so we can easily change it
    public function __construct(private string $key)
    {
        
    }

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
                                                $this->key,
                                                true);

        $signature =  $this->base64urlEncode($signature);

        return $header . "." . $payload . "." . $signature;

    }

    // decode, regular expression if matches return 1 else null
    public function decode(string $token): array
    {
        if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)/", $token, $matches) !== 1) {
            throw new InvalidArgumentException("invalid token format");
        }

        // decode
        $signature = hash_hmac("sha256",
                                                $matches["header"] . "." . $matches["payload"],
                                                $this->key,
                                                true);

        $signature_from_token = $this->base64urlDecode($matches["signature"]);

        // if tokens do not match
        if (!hash_equals($signature, $signature_from_token)) {
            throw new InvalidSignatureException("Signature doesn't match");
        }

        // if oka decode token
        $payload = json_decode($this->base64urlDecode($matches["payload"]), true);

        return $payload;
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

    private function base64urlDecode(string $text): string
    {
        return base64_decode(str_replace(["-", "_"], ["+", "/"], $text));
    }

    // generated encryption key 2D4B6150645367566B58703273357638792F423F4528482B4D6251655468576D

}