import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

type UseContactOptions = {
    include?: readonly string[];
};

export function useContact(id: number, { include = [] }: UseContactOptions = {}) {
    const normalized = [...include].sort();
    return useQuery({
        queryKey: ['contacts', 'detail', id, { include: normalized }],
        queryFn: () => {
            const qs = normalized.length ? `?include=${normalized.join(',')}` : '';
            return apiFetch<App.Contact.Data.ContactData>(`/api/contacts/${id}${qs}`);
        },
        enabled: Number.isFinite(id) && id > 0,
    });
}
