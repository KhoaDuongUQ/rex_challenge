import { useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

export function useCallContact() {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: (id: number) =>
            apiFetch<App.Contact.Data.CallOutcomeData>(`/api/contacts/${id}/call`, {
                method: 'POST',
            }),
        onSuccess: (_outcome, id) => {
            qc.invalidateQueries({ queryKey: ['contacts', 'detail', id] });
        },
    });
}
