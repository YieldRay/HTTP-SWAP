package main

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"net/url"
	"strings"
)

func HTTP_SWAP(httpSwap string) (res *http.Response, err error) {
	// def param
	var reqURL *url.URL
	var method string = "GET"
	var headers *http.Header = &http.Header{}
	var body string = ""
	// var redirect string = ""
	params := url.Values{}
	// parse from json
	var parsed any
	json.Unmarshal([]byte(httpSwap), &parsed)
	kvMap := parsed.(map[string]any)
	for k, v := range kvMap {
		if k == "url" {
			reqURL, err = url.Parse(v.(string))
		}
		if k == "method" {
			method = v.(string)
		}
		if k == "body" {
			body = v.(string)
		}
		if k == "headers" {
			headersMap := v.(map[string]any)
			for h, v := range headersMap {
				headers.Add(h, v.(string))
			}
		}
		if k == "params" {
			paramsMap := v.(map[string]any)
			for p, v := range paramsMap {
				params.Add(p, v.(string))
			}
		}
		reqURL.RawQuery = params.Encode()
		urlPath := reqURL.String()

		var req *http.Request
		req, err = http.NewRequest(method, urlPath, strings.NewReader(body))
		req.Header = *headers
		res, err = http.DefaultClient.Do(req)

	}
	return
}

// TEST
// $ go run go.go
func main() {
	r, _ := HTTP_SWAP(`{
		"url": "https://httpbingo.org/get",
		"params": {"k": "v"},
		"headers": {"X-TEST": "HTTP_SWAP TEST VALUE"}
	}`)
	u := r.Request.URL.String()
	body, _ := io.ReadAll(r.Body)
	fmt.Printf("Request: %s\n", u)
	fmt.Println(string(body))
}
