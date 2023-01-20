type Dict = { [key: string]: string };
type List = Array<[string, string]>;

interface HTTP_SWAP {
    url: string;
    method?: "GET" | "POST" | "HEAD" | "OPTIONS" | "PUT" | "PATCH" | "DELETE" | "TRACE" | "CONNECT";
    headers?: Dict | List;
    redirect?: "follow" | "manual" | "error";
    body?: string;
    form?: Dict;
    json?: any;
    query?: Dict | List;
}

function isHttpSwap(x: unknown): x is HTTP_SWAP {
    if (typeof x !== "object" || x === null) return false;
    return "url" in x;
}

function setContentTypeIfUnset(h: Dict, v: string) {
    if (
        !Object.keys(h)
            .map((k) => k.toLowerCase())
            .includes("content-type")
    )
        h["content-type"] = v;
}

export default function HTTP_SWAP(httpSwap: HTTP_SWAP | string) {
    if (typeof httpSwap === "string") httpSwap = JSON.parse(httpSwap);
    if (!isHttpSwap(httpSwap)) throw new Error("`url` is not set");

    const url = new URL(httpSwap.url);

    // handle 'query'
    let query: Array<[string, string]> = [];
    if (Array.isArray(httpSwap.query)) ({ query } = httpSwap);
    else if (httpSwap.query) query = Object.entries(httpSwap.query);
    query.forEach(([k, v]) => url.searchParams.append(k, v));

    // handle 'headers'
    let headers: { [key: string]: string } = {};
    if (Array.isArray(httpSwap.headers)) httpSwap.headers.forEach(([k, v]) => (headers[k] = v));
    else if (httpSwap.headers) ({ headers } = httpSwap);

    // handle 'method' 'body'
    let method: string = "GET";
    let body: string = "";
    if ("json" in httpSwap) {
        body = JSON.stringify(httpSwap.json);
        setContentTypeIfUnset(headers, "application/json");
        method = "POST";
    }
    if ("form" in httpSwap && typeof httpSwap.form === "object") {
        body = new URLSearchParams(httpSwap.form).toString();
        setContentTypeIfUnset(headers, "application/x-www-form-urlencoded");
        method = "POST";
    }
    if ("body" in httpSwap && typeof httpSwap.body === "string") {
        body = httpSwap.body;
        method = "POST";
    }
    if ("method" in httpSwap && typeof httpSwap.method === "string") {
        method = httpSwap.method;
    }
    /* redundant check just for making us clear */

    return fetch(url, {
        method,
        headers,
        body,
        redirect: httpSwap.redirect,
    });
}

// TEST (node>=18)
// $ deno run --allow-net ts.ts
// $ tsc ts; node ts; rm ts.js

/* passing an object is also valid
HTTP_SWAP(`{
    "url": "https://httpbingo.org/post",
    "json": { "k": "v" },
    "headers": { "X-TEST": "HTTP_SWAP TEST VALUE" }
}`)
*/

HTTP_SWAP({
    url: "https://httpbingo.org/post",
    json: { k1: "v1", k2: "v2" },
    headers: { "X-TEST": "HTTP_SWAP TEST VALUE" },
})
    .then((res) => {
        console.log(`${res.status} ${res.statusText} ${res.url}`);
        return res.text();
    })
    .then(console.log);
