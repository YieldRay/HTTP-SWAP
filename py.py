import json
from urllib import request, parse


def HTTP_SWAP(httpSwap: str) -> request._UrlopenRet:
    httpSwap = json.loads(httpSwap)
    body = httpSwap.get("body", "").encode()
    method = httpSwap.get("method", "GET")
    headersMap = httpSwap.get("headers", {})
    params = parse.urlencode(httpSwap.get("params", {}))
    if params:
        url = httpSwap["url"] + "?" + params
    else:
        url = httpSwap["url"]
    req = request.Request(url, data=body, method=method)
    for k, v in headersMap.items():
        req.add_header(k, v)
    return request.urlopen(req)


# TEST
# $ python -u py.py
r = HTTP_SWAP("""
{
    "url": "https://httpbingo.org/post",
    "method":"POST",
    "body":"bbb=aaa",
    "headers": {
        "user-agent": "HTTP_SWAP",
        "X-TEST": "HTTP_SWAP TEST VALUE"
    }
}
""")


with r as f:
    print(f.read().decode('utf-8'))
