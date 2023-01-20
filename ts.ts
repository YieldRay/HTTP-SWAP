interface HTTP_SWAP {
    url: string;
    method?: "GET" | "POST" | "HEAD" | "OPTIONS" | "PUT" | "PATCH" | "DELETE" | "TRACE" | "CONNECT";
    headers?: { [key: string]: string } | [string, string][];
    redirect?: "follow" | "manual" | "error";
    body?: string;
    query?: { [key: string]: string } | [string, string][];
}

function is_http_swap(x: unknown): x is HTTP_SWAP {
    if (typeof x !== "object" || x === null) return false;
    return "url" in x;
}

export default function HTTP_SWAP(httpSwap: HTTP_SWAP | string) {
    if (typeof httpSwap === "string") httpSwap = JSON.parse(httpSwap);
    if (!is_http_swap(httpSwap)) throw new Error("`url` is not set");

    const url = new URL(httpSwap.url);
    let query: [string, string][] = [];
    if (Array.isArray(httpSwap.query)) {
        ({ query } = httpSwap);
    } else if (httpSwap.query) {
        query = Object.entries(httpSwap.query);
    }
    query.forEach(([k, v]) => url.searchParams.append(k, v));
    let headers: { [key: string]: string } = {};
    if (Array.isArray(httpSwap.headers)) {
        httpSwap.headers.forEach(([k, v]) => (headers[k] = v));
    } else if (httpSwap.headers) {
        ({ headers } = httpSwap);
    }
    return fetch(url, {
        method: httpSwap.method,
        headers,
        redirect: httpSwap.redirect,
        body: httpSwap.body,
    });
}

// TEST (node>=18)
// $ deno run --allow-net ts.ts
// $ node ts.ts

/* passing an object is also valid
HTTP_SWAP({
    url: "https://httpbingo.org/get",
    query: { k: "v" },
    headers: { "X-TEST": "HTTP_SWAP TEST VALUE" },
});
*/

HTTP_SWAP(`{
    "url": "https://httpbingo.org/get",
    "query": { "k": "v" },
    "headers": { "X-TEST": "HTTP_SWAP TEST VALUE" }
}`)
    .then((res) => {
        console.log(`${res.status} ${res.statusText} ${res.url}`);
        return res.text();
    })
    .then(console.log);
