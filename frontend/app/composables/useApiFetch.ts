import { useFetch, useRuntimeConfig } from '#imports';
import type { AsyncData, UseFetchOptions } from 'nuxt/app';
import type { FetchError } from 'ofetch';

type MaybeComputedPath = string | (() => string);

export const useApiFetch = <T>(path: MaybeComputedPath, options: UseFetchOptions<T> = {}): AsyncData<T, FetchError<T>> => {
  const config = useRuntimeConfig();

  const resolvePath = () => {
    const raw = typeof path === 'function' ? path() : path;
    return raw.startsWith('/') ? raw : `/${raw}`;
  };

  const headers: HeadersInit = {
    accept: 'application/json',
    ...(options.headers as Record<string, string> | undefined),
  };

  const fetchOptions = {
    ...options,
    baseURL: config.public.apiBase,
    headers,
  } as UseFetchOptions<T>;

  return useFetch<T>(resolvePath as any, fetchOptions as any) as AsyncData<T, FetchError<T>>;
};
