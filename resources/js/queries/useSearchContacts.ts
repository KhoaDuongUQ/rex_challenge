import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

type SearchField = 'name' | 'phone' | 'email_domain';

interface SearchParams {
    q: string;
    field?: SearchField;
}

export function useSearchContacts({ q, field }: SearchParams) {
    return useQuery({
        queryKey: ['contacts', 'search', { q, field }],
        queryFn: () => {
            const params = new URLSearchParams({ q });
            if (field) params.set('field', field);
            return apiFetch<App.Contact.Data.ContactData[]>(`/api/contacts/search?${params}`);
        },
        enabled: q.length > 0,
    });
}
