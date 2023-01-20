import json
from urllib.request import urlopen, Request
from urllib.parse import urlparse, urlunparse, urlencode


def HTTP_SWAP(httpSwap: map | str):
    if type(httpSwap) == str:
        httpSwap = json.loads(httpSwap)
    body: bytes = httpSwap.get("body", "").encode()
    method: str = httpSwap.get("method", "GET")
    headersMap: dict = httpSwap.get("headers", {})
    url = urlparse(httpSwap["url"])
    query = urlencode(dict(url.params, **(httpSwap.get("query", {}))))
    url = urlunparse((url.scheme, url.netloc, url.path,
                     url.params, query, url.fragment))
    req = Request(url, data=body, method=method)
    for k, v in headersMap.items():
        req.add_header(k, v)
    return urlopen(req)


# TEST
# $ python -u py.py

# passing a json string is also valid
with HTTP_SWAP(
    {
        "url": "https://httpbingo.org/post",
        "query": {"q": "123"},
        "method": "POST",
        "body": "bbb=aaa",
        "headers": {
            "user-agent": "HTTP_SWAP",
            "X-TEST": "HTTP_SWAP TEST VALUE"
        }
    }
) as res:
    print(res.status, res.reason)
    print(dict(res.getheaders()))
    print(res.read().decode('utf-8'))
