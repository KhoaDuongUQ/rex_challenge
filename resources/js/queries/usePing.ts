import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

export function usePing() {
    return useQuery({
        queryKey: ['ping'],
        queryFn: () => apiFetch<App.Data.PingData>('/api/ping'),
    });
}
