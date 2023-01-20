# HTTP-SWAP

just for fun

```json
{
    "url": "<URL>",
    "method": "GET|POST|HEAD|OPTIONS|PUT|PATCH|DELETE|TRACE|CONNECT",
    "headers": { "<string>": "<string>" },
    "body": "<string>",
    "query": { "<string>": "<string>" }
}
```

Use a JSON schema to express a simple HTTP request  
With the power of the standard library of the programming language

This library ships a single function, which you need to pass a JSON  
And it returns a `response` (depending on the specific language)
