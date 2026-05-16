import { usePing } from './queries/usePing';

export default function Root() {
    const { data, isLoading, isError, error } = usePing();

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-6">
            <div className="max-w-xl w-full bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-8">
                <h1 className="text-2xl font-semibold text-slate-900">rex_test</h1>
                <p className="mt-1 text-sm text-slate-500">
                    Laravel 13 + React + Tailwind + TanStack Query
                </p>

                <div className="mt-6 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4 font-mono text-sm">
                    {isLoading && <span className="text-slate-500">Loading /api/ping…</span>}
                    {isError && <span className="text-rose-600">Error: {error.message}</span>}
                    {data && (
                        <dl className="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-slate-800">
                            <dt className="text-slate-500">message</dt><dd>{data.message}</dd>
                            <dt className="text-slate-500">app</dt><dd>{data.app}</dd>
                            <dt className="text-slate-500">time</dt><dd>{data.time}</dd>
                        </dl>
                    )}
                </div>
            </div>
        </div>
    );
}
