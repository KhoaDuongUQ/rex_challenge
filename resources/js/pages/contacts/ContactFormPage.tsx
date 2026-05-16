import { FormEvent, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { useContact } from '../../queries/useContact';
import { useUpsertContact } from '../../mutations/useUpsertContact';
import { ApiError } from '../../lib/apiFetch';

type FormState = {
    name: string;
    phone: string;
    email: string;
};

const EMPTY: FormState = { name: '', phone: '', email: '' };

export default function ContactFormPage() {
    const { id: idParam } = useParams<{ id: string }>();
    const id = idParam ? Number(idParam) : null;
    const isEdit = id !== null && Number.isFinite(id) && id > 0;
    const navigate = useNavigate();

    const { data: existing, isLoading: isLoadingExisting } = useContact(isEdit ? id! : 0);
    const mutation = useUpsertContact();

    const [form, setForm] = useState<FormState>(EMPTY);
    const [hydratedId, setHydratedId] = useState<number | null>(null);

    if (isEdit && existing && existing.id !== hydratedId) {
        setHydratedId(existing.id);
        setForm({
            name: existing.name,
            phone: existing.phone ?? '',
            email: existing.email ?? '',
        });
    }

    const fieldErrors = extractFieldErrors(mutation.error);

    const onSubmit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        mutation.mutate(
            {
                id: isEdit ? id! : null,
                name: form.name,
                phone: form.phone.trim() || null,
                email: form.email.trim() || null,
            },
            {
                onSuccess: (contact) => navigate(`/contacts/${contact.id}`),
            },
        );
    };

    if (isEdit && isLoadingExisting) {
        return (
            <div className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p className="text-sm text-slate-500">Loading…</p>
            </div>
        );
    }

    return (
        <form
            onSubmit={onSubmit}
            className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
        >
            <h1 className="text-xl font-semibold text-slate-900">
                {isEdit ? 'Edit contact' : 'New contact'}
            </h1>

            <div className="mt-6 space-y-4">
                <Field
                    label="Name"
                    value={form.name}
                    onChange={(v) => setForm((f) => ({ ...f, name: v }))}
                    error={fieldErrors.name}
                    required
                />
                <Field
                    label="Phone"
                    value={form.phone}
                    onChange={(v) => setForm((f) => ({ ...f, phone: v }))}
                    error={fieldErrors.phone}
                    placeholder="AU or NZ number, e.g. +61412345678"
                />
                <Field
                    label="Email"
                    type="email"
                    value={form.email}
                    onChange={(v) => setForm((f) => ({ ...f, email: v }))}
                    error={fieldErrors.email}
                />
            </div>

            {mutation.isError && !hasFieldErrors(fieldErrors) && (
                <p className="mt-4 text-sm text-rose-600">{mutation.error.message}</p>
            )}

            <div className="mt-6 flex items-center gap-2">
                <button
                    type="submit"
                    disabled={mutation.isPending}
                    className="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    {mutation.isPending ? 'Saving…' : isEdit ? 'Save changes' : 'Create contact'}
                </button>
                <Link
                    to={isEdit ? `/contacts/${id}` : '/contacts'}
                    className="text-sm text-slate-600 hover:text-slate-900"
                >
                    Cancel
                </Link>
            </div>
        </form>
    );
}

function Field({
    label,
    value,
    onChange,
    error,
    type = 'text',
    placeholder,
    required,
}: {
    label: string;
    value: string;
    onChange: (v: string) => void;
    error?: string;
    type?: string;
    placeholder?: string;
    required?: boolean;
}) {
    return (
        <label className="block">
            <span className="block text-sm font-medium text-slate-700">
                {label}
                {required && <span className="text-rose-600"> *</span>}
            </span>
            <input
                type={type}
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
                className={`mt-1 w-full rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-900 ring-1 ring-inset placeholder:text-slate-400 focus:bg-white ${
                    error
                        ? 'ring-rose-400 focus:ring-rose-500'
                        : 'ring-slate-200 focus:ring-indigo-500'
                }`}
            />
            {error && <span className="mt-1 block text-xs text-rose-600">{error}</span>}
        </label>
    );
}

type FieldErrors = Partial<Record<'name' | 'phone' | 'email' | 'id', string>>;

function extractFieldErrors(error: unknown): FieldErrors {
    if (!(error instanceof ApiError) || error.status !== 422) {
        return {};
    }
    const body = error.body;
    if (!body || typeof body !== 'object' || !('errors' in body)) {
        return {};
    }
    const errors = (body as { errors: unknown }).errors;
    if (!errors || typeof errors !== 'object') {
        return {};
    }
    const out: FieldErrors = {};
    for (const key of ['name', 'phone', 'email', 'id'] as const) {
        const messages = (errors as Record<string, unknown>)[key];
        if (Array.isArray(messages) && typeof messages[0] === 'string') {
            out[key] = messages[0];
        }
    }
    return out;
}

function hasFieldErrors(errors: FieldErrors): boolean {
    return Object.values(errors).some((v) => typeof v === 'string');
}
