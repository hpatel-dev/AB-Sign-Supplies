import type { AsyncData, UseFetchOptions } from 'nuxt/app';
import type { FetchError } from 'ofetch';

export interface CompanyInfo {
  site_name: string | null
  tagline: string | null
  logo_url: string | null
  about_us: string
  contact_email: string
  contact_phone: string
  address: string
  google_map_embed: string | null
}

export const useCompanyInfo = (
  options: UseFetchOptions<CompanyInfo | null> = {},
): AsyncData<CompanyInfo | null, FetchError> => {
  return useApiFetch<CompanyInfo | null>('/company', {
    key: 'company-info',
    ...options,
  }) as AsyncData<CompanyInfo | null, FetchError>;
}
