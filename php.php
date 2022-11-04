<?php
function HTTP_SWAP(string $httpSwap)
{
    $httpSwap = json_decode($httpSwap, true);
    // def param (for higher version of php, use ??)
    $url  = $httpSwap["url"];
    $method = array_key_exists("method", $httpSwap)  ? $httpSwap["method"] : "GET";
    $params = array_key_exists("params", $httpSwap) ? $httpSwap["params"] : [];
    $headers = array_key_exists("headers", $httpSwap) ? $httpSwap["headers"] : [];
    $body = array_key_exists("body", $httpSwap) ? $httpSwap["body"] : "";
    // do
    $headersArray = [];
    foreach ($headers as $key => $value) {
        $headersArray = "$key: $value";
    }
    if ($params) $url = "$url?";
    foreach ($params as $key => $value) {
        $url = "${url}${key}=${value}&";
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);

    return $ch;
}

// TEST
// $ php.php
$ch =  HTTP_SWAP(<<<STR
{
    "url": "https://httpbingo.org/get",
    "params": {"k": "v"},
    "headers": {"X-TEST": "HTTP_SWAP TEST VALUE"}
}
STR);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
curl_close($ch);
