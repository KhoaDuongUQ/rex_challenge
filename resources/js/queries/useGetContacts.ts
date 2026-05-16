import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

interface GetContactsParams {
    search?: string;
}

export function useGetContacts({ search }: GetContactsParams = {}) {
    return useQuery({
        queryKey: ['contacts', 'list', { search }],
        queryFn: () => {
            const qs = search ? `?${new URLSearchParams({ search })}` : '';
            return apiFetch<App.Contact.Data.ContactData[]>(`/api/contacts${qs}`);
        },
    });
}
