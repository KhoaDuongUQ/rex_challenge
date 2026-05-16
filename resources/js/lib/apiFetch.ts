type ApiFetchOptions = Omit<RequestInit, 'body'> & { body?: unknown };

export class ApiError extends Error {
    constructor(
        message: string,
        public status: number,
        public body: unknown,
    ) {
        super(message);
        this.name = 'ApiError';
    }
}

export async function apiFetch<T = unknown>(
    url: string,
    options: ApiFetchOptions = {},
): Promise<T> {
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
        let parsed: unknown = null;
        try {
            parsed = await response.json();
        } catch {
            // response had no JSON body
        }
        let message = `Request failed: ${response.status} ${response.statusText}`;
        if (
            parsed &&
            typeof parsed === 'object' &&
            'message' in parsed &&
            typeof (parsed as { message: unknown }).message === 'string'
        ) {
            message = (parsed as { message: string }).message;
        }
        throw new ApiError(message, response.status, parsed);
    }

    if (response.status === 204 || response.headers.get('content-length') === '0') {
        return undefined as T;
    }

    return response.json() as Promise<T>;
}
