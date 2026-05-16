import { useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

export function useDeleteContact() {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: (id: number) => apiFetch<void>(`/api/contacts/${id}`, { method: 'DELETE' }),
        onSuccess: (_void, id) => {
            qc.invalidateQueries({ queryKey: ['contacts'] });
            qc.removeQueries({ queryKey: ['contacts', 'detail', id] });
        },
    });
}
