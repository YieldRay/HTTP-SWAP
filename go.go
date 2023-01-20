package main

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"net/url"
	"strings"
)

func setContentTypeIfUnset(h *http.Header, v string) {
	if h.Get("Content-Type") == "" {
		h.Set("Content-Type", v)
	}
}

func HTTP_SWAP(httpSwap string) (res *http.Response, err error) {
	var reqURL *url.URL
	var method string
	var headers *http.Header = &http.Header{}
	var body string = ""
	// var redirect string = ""
	query := url.Values{}

	var parsed interface{}
	if err = json.Unmarshal([]byte(httpSwap), &parsed); err != nil {
		return nil, err
	}

	for k, v := range parsed.(map[string]any) {
		if k == "url" {
			if reqURL, err = url.Parse(v.(string)); err != nil {
				return nil, err
			}
		}
		if k == "method" {
			method = v.(string)
		}
		if k == "headers" {
			for k, v := range v.(map[string]any) {
				headers.Add(k, v.(string))
			}
		}
		if k == "query" {
			for k, v := range v.(map[string]any) {
				query.Add(k, v.(string))
			}
		}

	}

	for k, v := range parsed.(map[string]any) {
		if k == "json" {
			if bytes, err := json.Marshal(v); err != nil {
				return nil, err
			} else {
				body = string(bytes)
			}
			setContentTypeIfUnset(headers, "application/json")
			if method == "" {
				method = "POST"
			}
		}
		if k == "form" {
			form := url.Values{}
			for k, v := range v.(map[string]any) {
				form.Add(k, v.(string))
			}
			body = form.Encode()
			setContentTypeIfUnset(headers, "application/x-www-form-urlencoded")
			if method == "" {
				method = "POST"
			}
		}
		if k == "body" {
			body = v.(string)
			if method == "" {
				method = "POST"
			}
		}
	}

	if method == "" {
		method = "GET"
	}

	reqURL.RawQuery = query.Encode()
	urlString := reqURL.String()

	var req *http.Request
	req, err = http.NewRequest(method, urlString, strings.NewReader(body))
	req.Header = *headers
	res, err = http.DefaultClient.Do(req)

	return res, err
}

// TEST
// $ go run go.go

func main() {
	r, err := HTTP_SWAP(`{
		"url": "https://httpbingo.org/post",
		"query": {"q": "a"},
		"form": { "k1": "v1", "k2": "v2" },
		"headers": {"X-TEST": "HTTP_SWAP TEST VALUE"}
	}`)
	if err != nil {
		fmt.Println(err)
	}
	body, _ := io.ReadAll(r.Body)
	fmt.Printf("Request: %s\n", r.Request.URL.String())
	fmt.Println(string(body))
}
