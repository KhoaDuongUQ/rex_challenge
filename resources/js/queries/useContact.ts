import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

export function useContact(id: number) {
    return useQuery({
        queryKey: ['contacts', 'detail', id],
        queryFn: () => apiFetch<App.Contact.Data.ContactData>(`/api/contacts/${id}`),
        enabled: Number.isFinite(id) && id > 0,
    });
}
