<?php

$ch = curl_init();

$headers = [
    "Authorization: Client-ID fds56f4sdgdfg4dfg4dfg"
];
// curl_setopt($ch, CURLOPT_URL, "https://randomuser.me/api");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt_array($ch, [
    // api access url
    CURLOPT_URL => "https://randomuser.me/api",
    // return response data
    CURLOPT_RETURNTRANSFER => true,
    // include our headers
    CURLOPT_HTTPHEADER => $headers,
    // get response headers
    CURLOPT_HEADER => true
]);

// store response to variable
$response = curl_exec($ch);

// get content type
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

// get api response code
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo $status_code , "\n";
echo $content_type, "\n";
echo $response , "\n";
?>