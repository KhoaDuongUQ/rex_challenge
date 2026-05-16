import { useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

export function useUpsertContact() {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: (input: App.Contact.Data.UpsertContactData) =>
            apiFetch<App.Contact.Data.ContactData>('/api/contacts', {
                method: 'POST',
                body: input,
            }),
        onSuccess: (contact) => {
            qc.invalidateQueries({ queryKey: ['contacts'] });
            qc.setQueriesData<App.Contact.Data.ContactData>(
                { queryKey: ['contacts', 'detail', contact.id] },
                (prev) => (prev ? { ...contact, calls: prev.calls ?? contact.calls } : contact),
            );
        },
    });
}
