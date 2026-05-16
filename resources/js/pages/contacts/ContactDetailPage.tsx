import { Link, useNavigate, useParams } from 'react-router-dom';
import { useContact } from '../../queries/useContact';
import { useCallContact } from '../../mutations/useCallContact';
import { useDeleteContact } from '../../mutations/useDeleteContact';

const OUTCOME_LABEL: Record<App.Contact.Enums.CallOutcome, string> = {
    connected: 'Connected',
    no_answer: 'No answer',
    busy: 'Busy',
    voicemail: 'Voicemail',
    failed: 'Failed',
};

const OUTCOME_BADGE: Record<App.Contact.Enums.CallOutcome, string> = {
    connected: 'bg-emerald-100 text-emerald-800',
    no_answer: 'bg-slate-100 text-slate-700',
    busy: 'bg-amber-100 text-amber-800',
    voicemail: 'bg-indigo-100 text-indigo-800',
    failed: 'bg-rose-100 text-rose-800',
};

export default function ContactDetailPage() {
    const { id: idParam } = useParams<{ id: string }>();
    const id = Number(idParam);
    const navigate = useNavigate();

    const { data: contact, isLoading, isError, error } = useContact(id, { include: ['calls'] });
    const callMutation = useCallContact();
    const deleteMutation = useDeleteContact();

    if (!Number.isFinite(id) || id <= 0) {
        return (
            <Card>
                <p className="text-sm text-rose-600">Invalid contact id.</p>
            </Card>
        );
    }

    if (isLoading) {
        return (
            <Card>
                <p className="text-sm text-slate-500">Loading…</p>
            </Card>
        );
    }

    if (isError) {
        return (
            <Card>
                <p className="text-sm text-rose-600">Error: {error.message}</p>
            </Card>
        );
    }

    if (!contact) {
        return null;
    }

    const handleDelete = () => {
        if (!window.confirm(`Delete ${contact.name}?`)) {
            return;
        }
        deleteMutation.mutate(contact.id, {
            onSuccess: () => navigate('/contacts'),
        });
    };

    const handleCall = () => {
        callMutation.mutate(contact.id);
    };

    const calls = contact.calls ?? [];

    return (
        <div className="space-y-4">
            <Card>
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-xl font-semibold text-slate-900">{contact.name}</h1>
                        <dl className="mt-4 grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                            <dt className="text-slate-500">Phone</dt>
                            <dd className="text-slate-900">{contact.phone ?? '—'}</dd>
                            <dt className="text-slate-500">Email</dt>
                            <dd className="text-slate-900">{contact.email ?? '—'}</dd>
                            <dt className="text-slate-500">Created</dt>
                            <dd className="text-slate-900">
                                {new Date(contact.createdAt).toLocaleString()}
                            </dd>
                            <dt className="text-slate-500">Updated</dt>
                            <dd className="text-slate-900">
                                {new Date(contact.updatedAt).toLocaleString()}
                            </dd>
                        </dl>
                    </div>
                </div>

                <div className="mt-6 flex flex-wrap gap-2">
                    <button
                        type="button"
                        onClick={handleCall}
                        disabled={callMutation.isPending}
                        className="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
                    >
                        {callMutation.isPending ? 'Calling…' : 'Call'}
                    </button>
                    <Link
                        to={`/contacts/${contact.id}/edit`}
                        className="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-900 hover:bg-slate-200"
                    >
                        Edit
                    </Link>
                    <button
                        type="button"
                        onClick={handleDelete}
                        disabled={deleteMutation.isPending}
                        className="rounded-lg bg-rose-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-50"
                    >
                        {deleteMutation.isPending ? 'Deleting…' : 'Delete'}
                    </button>
                    <Link
                        to="/contacts"
                        className="ml-auto text-sm text-slate-600 hover:text-slate-900"
                    >
                        ← Back
                    </Link>
                </div>
            </Card>

            {callMutation.isError && (
                <Card>
                    <p className="text-sm text-rose-600">
                        Call failed: {callMutation.error.message}
                    </p>
                </Card>
            )}

            {callMutation.data && (
                <Card>
                    <h2 className="text-sm font-semibold text-slate-900">Last call</h2>
                    <dl className="mt-3 grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                        <dt className="text-slate-500">Outcome</dt>
                        <dd className="text-slate-900">
                            {OUTCOME_LABEL[callMutation.data.outcome]}
                        </dd>
                        <dt className="text-slate-500">Called at</dt>
                        <dd className="text-slate-900">
                            {new Date(callMutation.data.calledAt).toLocaleString()}
                        </dd>
                        <dt className="text-slate-500">Call URL</dt>
                        <dd className="truncate">
                            <a
                                href={callMutation.data.callUrl}
                                className="text-indigo-600 hover:text-indigo-700"
                                target="_blank"
                                rel="noreferrer"
                            >
                                {callMutation.data.callUrl}
                            </a>
                        </dd>
                    </dl>
                </Card>
            )}

            <Card>
                <h2 className="text-sm font-semibold text-slate-900">
                    Call history <span className="text-slate-400">({calls.length})</span>
                </h2>

                {calls.length === 0 ? (
                    <p className="mt-3 text-sm text-slate-500">No calls yet.</p>
                ) : (
                    <ul className="mt-3 divide-y divide-slate-100">
                        {calls.map((call) => (
                            <li
                                key={call.id}
                                className="flex items-center justify-between gap-4 py-2 text-sm"
                            >
                                <span
                                    className={`inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${OUTCOME_BADGE[call.outcome]}`}
                                >
                                    {OUTCOME_LABEL[call.outcome]}
                                </span>
                                <time className="text-xs text-slate-500" dateTime={call.calledAt}>
                                    {new Date(call.calledAt).toLocaleString()}
                                </time>
                            </li>
                        ))}
                    </ul>
                )}
            </Card>
        </div>
    );
}

function Card({ children }: { children: React.ReactNode }) {
    return (
        <div className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">{children}</div>
    );
}
