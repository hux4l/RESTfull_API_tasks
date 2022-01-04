<?php

// init curl
$ch = curl_init();

// response headers array
$response_headers = [];

// callback function for response headers
$header_callback = function($ch, $header) use (&$response_headers) {
    $len = strlen($header);

    // split head in two parts by :
    $parts = explode(":", $header, 2);

    // if is less than 2 return header
    if (count($parts) < 2) {
        return $len;
    }

    // header key will be header name, and value will be the header value
    $response_headers[$parts[0]] = trim($parts[1]);

    return $len;
};

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
    //CURLOPT_HEADER => true
    // header function to get headers
    CURLOPT_HEADERFUNCTION => $header_callback
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

print_r($response_headers);

echo $response , "\n";
?>