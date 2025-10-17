import type { UseFetchOptions } from 'nuxt/app';

export const useApiFetch = <T>(path: string, options: UseFetchOptions<T> = {}) => {
  const config = useRuntimeConfig();
  const normalizedPath = path.startsWith('/') ? path : `/${path}`;

  return useFetch<T>(`${config.public.apiBase}${normalizedPath}`, {
    ...options,
    headers: {
      accept: 'application/json',
      ...(options.headers ?? {}),
    },
  });
};
