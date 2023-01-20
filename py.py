import json
from urllib.request import urlopen, Request
from urllib.parse import urlparse, urlunparse, urlencode


def _set_content_type_if_unset(h: dict, v: str):
    if "content-type" not in map(lambda k: k.lower(), h.keys()):
        h["content-type"] = v


def HTTP_SWAP(httpSwap: dict | str):
    if type(httpSwap) == str:
        httpSwap: dict = json.loads(httpSwap)

    # raise Exception('Cannot set "body","form","json" at the same time')

    method: str = "GET"

    headersMap: dict = httpSwap.get("headers", {})

    data: bytes | None = None

    try:
        json_body: dict | str = httpSwap["json"]
        data = json.dumps(json_body).encode()
        _set_content_type_if_unset(headersMap, "application/json")
        method = "POST"
    except TypeError as e:
        raise e
    except:
        pass

    try:
        form: dict | str = httpSwap["form"]
        if type(form) == str:
            data = form.encode()
        if type(form) == dict:
            data = urlencode(form).encode()
        _set_content_type_if_unset(
            headersMap, "application/x-www-form-urlencoded")
        method = "POST"
    except:
        pass

    try:
        body: str = httpSwap["body"]
        data = body.encode()
        method = "POST"
    except:
        pass

    if "method" in httpSwap:
        method = httpSwap.get("method")
    url = urlparse(httpSwap["url"])
    query = urlencode(dict(url.params, **(httpSwap.get("query", {}))))
    url = urlunparse((url.scheme, url.netloc, url.path,
                     url.params, query, url.fragment))

    req = Request(url, data=data, method=method)

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
        "json": {"abc": "def", "ghi": "jkl"},
        "headers": {
            "user-agent": "HTTP_SWAP",
            "X-TEST": "HTTP_SWAP TEST VALUE"
        }
    }
) as res:
    print(res.status, res.reason)
    print(dict(res.getheaders()))
    print(res.read().decode('utf-8'))
