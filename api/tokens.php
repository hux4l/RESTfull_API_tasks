<?php

// return access token
// in JWT need to be exactly needed names
// exp adding expire time to token, with 300 seconds
$payload = [
    "sub" => $user["id"],
    "name" => $user["name"],
    "exp" => time() + 300
];


$access_token = $codec->encode($payload);

$refresh_token = $codec->encode([
    "sub" => $user["id"],
    "exp" => time() + 432000
]);

echo json_encode([ "access_token" => $access_token,
                                "refresh_token" => $refresh_token]);