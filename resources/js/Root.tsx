import { Link, Navigate, Route, Routes } from 'react-router-dom';
import ContactsListPage from './pages/contacts/ContactsListPage';
import ContactDetailPage from './pages/contacts/ContactDetailPage';
import ContactFormPage from './pages/contacts/ContactFormPage';

export default function Root() {
    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <header className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-3xl items-center justify-between px-6 py-4">
                    <Link to="/contacts" className="text-lg font-semibold text-slate-900">
                        rex_test
                    </Link>
                    <nav className="text-sm text-slate-600">
                        <Link to="/contacts" className="hover:text-slate-900">
                            Contacts
                        </Link>
                    </nav>
                </div>
            </header>

            <main className="mx-auto max-w-3xl px-6 py-8">
                <Routes>
                    <Route path="/" element={<Navigate to="/contacts" replace />} />
                    <Route path="/contacts" element={<ContactsListPage />} />
                    <Route path="/contacts/new" element={<ContactFormPage />} />
                    <Route path="/contacts/:id" element={<ContactDetailPage />} />
                    <Route path="/contacts/:id/edit" element={<ContactFormPage />} />
                    <Route path="*" element={<NotFound />} />
                </Routes>
            </main>
        </div>
    );
}

function NotFound() {
    return (
        <div className="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <h1 className="text-xl font-semibold text-slate-900">Not found</h1>
            <p className="mt-2 text-sm text-slate-600">
                The page you&apos;re looking for doesn&apos;t exist.
            </p>
            <Link
                to="/contacts"
                className="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700"
            >
                Back to contacts
            </Link>
        </div>
    );
}
