<?php
function HTTP_SWAP(string $httpSwap, $callback = true)
{
    $httpSwap = json_decode($httpSwap, true);
    // def param (for higher version of php, use ??)
    $url  = $httpSwap["url"];
    $method = array_key_exists("method", $httpSwap)  ? $httpSwap["method"] : "GET";
    $params = array_key_exists("params", $httpSwap) ? $httpSwap["params"] : [];
    $headers = array_key_exists("headers", $httpSwap) ? $httpSwap["headers"] : [];
    $redirect = array_key_exists("redirect", $httpSwap) ? $httpSwap["headers"] : "follow";
    $body = array_key_exists("body", $httpSwap) ? $httpSwap["body"] : "";
    // do
    $headersArray = [];
    foreach ($headers as $key => $value) {
        $headersArray[$key] = "$key: $value";
    }
    if ($params) $url = "$url?";
    foreach ($params as $key => $value) {
        $url = "${url}${key}=${value}&";
    }
    if ($params) $url = substr($url, 0, strlen($url) - 1);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($body)    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
    if ($redirect == "follow") curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($redirect == "manual") curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    // if ($redirect == "error")

    //! Handle $callback, which can be bool or function
    if ($callback instanceof Closure) $callback($ch);
    else {
        if ($callback === true) curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($callback === false) curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    }
    $resp =  curl_exec($ch);
    curl_close($ch);
    return $resp;
}

// TEST
// $ php.php
$res =  HTTP_SWAP(<<<STR
{
    "url": "https://httpbingo.org/get",
    "params": {"k": "v"},
    "headers": {"X-TEST": "HTTP_SWAP TEST VALUE"}
}
STR);
print_r(json_decode($res));
