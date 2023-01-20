# HTTP-SWAP

just for fun

```json
{
    "url": "<URL>",
    // "method", if unset, will be "GET" by default, when one of "body","form","json" is set, will be "POST"
    "method": "GET|POST|HEAD|OPTIONS|PUT|PATCH|DELETE|TRACE|CONNECT",
    "headers": { "<string>": "<string>" },
    // "raw":"<base64 string>", # UNIMPLEMENTED YET
    "body": "string",
    // `headers.content-type` will be "application/json"
    // you can overwrite this manually
    "json": { "<string>": "<string>" },
    // `headers.content-type` will be "application/x-www-form-urlencoded"
    // you can overwrite this manually
    "form": { "<string>": "<string>" },
    // you can set "query" rather than add query to "url" directly
    // it is not recommend that you set both of them at the same time
    "query": { "<string>": "<string>" }
}
```

Use a JSON schema to express a simple HTTP request  
With the power of the standard library of the programming language

This library ships a single function, which you need to pass a JSON  
And it returns a `response` (depending on the specific language)

# Example

A simple GET request can be

```json
{
    "url": "https://example.net/",
    "query": { "q": "a" }
}
```

```
GET /?q=a
Host: example.net
```

A simple POST request can be

```json
{
    "url": "https://example.net/",
    "form": { "k": "v" }
}
```

```
POST /
Host: example.net
Content-Type: application/json

k=v
```
