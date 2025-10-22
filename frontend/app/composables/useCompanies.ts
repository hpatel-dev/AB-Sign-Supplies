import type { AsyncData, UseFetchOptions } from 'nuxt/app';
import type { FetchError } from 'ofetch';

export interface CompanySummary {
  slug: string
  name: string
  tagline?: string | null
  summary?: string | null
  logo_url?: string | null
  sort_order: number
}

export interface CompanyDetail extends CompanySummary {
  overview?: string | null
  contact: {
    email?: string | null
    phone?: string | null
    address?: string | null
    website?: string | null
  }
  services: Array<{
    title: string
    description?: string | null
  }>
}

export const useCompanies = (
  options: UseFetchOptions<CompanySummary[]> = {},
): AsyncData<CompanySummary[], FetchError> => {
  return useApiFetch<CompanySummary[]>('/company-profiles', {
    key: 'company-profiles',
    default: () => [],
    ...options,
  }) as AsyncData<CompanySummary[], FetchError>
}

export const useCompanyProfile = (
  slug: () => string,
  options: UseFetchOptions<CompanyDetail> = {},
): AsyncData<CompanyDetail, FetchError> => {
  return useApiFetch<CompanyDetail>(() => `/company-profiles/${slug()}`, {
    key: () => `company-profile-${slug()}`,
    watch: [slug],
    ...options,
  }) as AsyncData<CompanyDetail, FetchError>
}
