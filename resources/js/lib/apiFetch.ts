type ApiFetchOptions = Omit<RequestInit, 'body'> & { body?: unknown };

export async function apiFetch<T = unknown>(url: string, options: ApiFetchOptions = {}): Promise<T> {
    const method = (options.method ?? 'GET').toUpperCase();
    const headers = new Headers(options.headers);
    headers.set('Accept', 'application/json');

    let body: BodyInit | undefined;
    if (options.body !== undefined) {
        body = JSON.stringify(options.body);
        headers.set('Content-Type', 'application/json');
    }

    if (method !== 'GET' && method !== 'HEAD') {
        const tokenEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        if (tokenEl) {
            headers.set('X-CSRF-TOKEN', tokenEl.content);
        }
    }

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        method,
        headers,
        body,
    });

    if (!response.ok) {
        throw new Error(`Request failed: ${response.status} ${response.statusText}`);
    }

    if (response.status === 204 || response.headers.get('content-length') === '0') {
        return undefined as T;
    }

    return response.json() as Promise<T>;
}
