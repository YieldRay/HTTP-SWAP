interface HTTP_SWAP {
    url: string;
    method?: "GET" | "POST" | "HEAD" | "OPTIONS" | "PUT" | "PATCH" | "DELETE" | "TRACE" | "CONNECT";
    headers?: { [key: string]: string } | [string, string][];
    redirect?: "follow" | "manual" | "error";
    body?: string;
    params?: { [key: string]: string } | [string, string][];
}

export default function HTTP_SWAP(httpSwap: HTTP_SWAP) {
    const url = new URL(httpSwap.url);
    let params: [string, string][] = [];
    if (Array.isArray(httpSwap.params)) {
        ({ params } = httpSwap);
    } else if (httpSwap.params) {
        params = Object.entries(httpSwap.params);
    }
    params.forEach(([k, v]) => url.searchParams.append(k, v));
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

HTTP_SWAP({
    url: "https://httpbingo.org/get",
    params: { k: "v" },
    headers: { "X-TEST": "HTTP_SWAP TEST VALUE" },
})
    .then((res) => {
        console.log("Request: %s", res.url);
        return res.text();
    })
    .then(console.log);
