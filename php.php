<?php

function _setContentTypeIfUnset(array $h, string $v)
{
    foreach ($h as $key => $value) {
        if (strtolower($key) === "content-type") {
            $h[$key] = $v;
        }
    }
}

function HTTP_SWAP(array|string $httpSwap, $callback = true)
{
    if (is_string($httpSwap))
        $httpSwap = json_decode($httpSwap, true);

    $url  =  parse_url($httpSwap["url"]);
    $method = "";
    $query = array_key_exists("query", $httpSwap) ? $httpSwap["query"] : [];
    $headers = array_key_exists("headers", $httpSwap) ? $httpSwap["headers"] : [];
    $redirect = array_key_exists("redirect", $httpSwap) ? $httpSwap["headers"] : "follow";
    $body =  null;


    if (array_key_exists("json", $httpSwap)) {
        $body = json_encode($httpSwap["json"]);
        _setContentTypeIfUnset($headers, "application/json");
        $method = "POST";
    }
    if (array_key_exists("form", $httpSwap)) {
        $body = http_build_query($httpSwap["form"]);
        _setContentTypeIfUnset($headers, "application/x-www-form-urlencoded");
        $method = "POST";
    }
    if (array_key_exists("body", $httpSwap)) {
        $body =  $httpSwap["body"];
        $method = "POST";
    }

    $method = array_key_exists("method", $httpSwap) ? $httpSwap["method"] : $method;
    $query = http_build_query($query);
    $url = sprintf("%s://%s%s?%s", $url["scheme"], $url["host"], $url["path"], $query);
    $headersArray = [];
    foreach ($headers as $key => $value) array_push($headersArray, sprintf("%s: %s", strtolower($key), $value));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
    if ($redirect == "follow") curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($redirect == "manual") curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    // if ($redirect == "error")

    //! Handle $callback, which can be a bool or function
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

/* passing an array is also valid
$res =  HTTP_SWAP(
    [
        "url" => "https://httpbingo.org/get",
        "query" => ["k" => "v"],
        "headers" => ["X-TEST" => "HTTP_SWAP TEST VALUE"]
    ]
);
*/
$res =  HTTP_SWAP(<<<JSON
{
    "url": "https://httpbingo.org/get",
    "query": {"k": "v"},
    "headers": {"X-TEST": "HTTP_SWAP TEST VALUE"}
}
JSON);
print_r(json_decode($res));
