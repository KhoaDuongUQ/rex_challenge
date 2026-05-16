import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { useGetContacts } from '../../queries/useGetContacts';

export default function ContactsListPage() {
    const [searchInput, setSearchInput] = useState('');
    const [search, setSearch] = useState('');

    useEffect(() => {
        const t = setTimeout(() => setSearch(searchInput.trim()), 200);
        return () => clearTimeout(t);
    }, [searchInput]);

    const { data, isLoading, isError, error } = useGetContacts(search ? { search } : undefined);

    return (
        <div className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div className="flex items-center justify-between gap-4">
                <h1 className="text-xl font-semibold text-slate-900">Contacts</h1>
                <Link
                    to="/contacts/new"
                    className="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    New contact
                </Link>
            </div>

            <input
                type="search"
                value={searchInput}
                onChange={(e) => setSearchInput(e.target.value)}
                placeholder="Search by name, phone, or email"
                className="mt-4 w-full rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-indigo-500"
            />

            <div className="mt-4">
                {isLoading && <p className="py-6 text-sm text-slate-500">Loading…</p>}
                {isError && <p className="py-6 text-sm text-rose-600">Error: {error.message}</p>}
                {data && data.length === 0 && (
                    <p className="py-6 text-sm text-slate-500">No contacts found.</p>
                )}
                {data && data.length > 0 && (
                    <ul className="divide-y divide-slate-100">
                        {data.map((contact) => (
                            <ContactRow key={contact.id} contact={contact} />
                        ))}
                    </ul>
                )}
            </div>
        </div>
    );
}

function ContactRow({ contact }: { contact: App.Contact.Data.ContactData }) {
    return (
        <li>
            <Link
                to={`/contacts/${contact.id}`}
                className="flex items-center justify-between gap-4 py-3 hover:bg-slate-50"
            >
                <div className="min-w-0">
                    <p className="truncate text-sm font-medium text-slate-900">{contact.name}</p>
                    <p className="truncate text-xs text-slate-500">
                        {contact.phone ?? '—'} · {contact.email ?? '—'}
                    </p>
                </div>
                <p className="shrink-0 text-xs text-slate-400">
                    {new Date(contact.updatedAt).toLocaleDateString()}
                </p>
            </Link>
        </li>
    );
}
