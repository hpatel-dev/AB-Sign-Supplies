import { defineEventHandler, getRequestURL, setHeader } from 'h3'
import { ofetch } from 'ofetch'
import { useRuntimeConfig } from '#imports'

interface ProductSummary {
  id: number
  updated_at?: string | null
  created_at?: string | null
}

interface ProductIndexResponse {
  data?: ProductSummary[]
  meta?: {
    current_page?: number
    last_page?: number
  }
}

interface CompanySummary {
  slug: string
  updated_at?: string | null
}

interface SitemapEntry {
  loc: string
  lastmod?: string
  changefreq?: string
  priority?: string
}

export default defineEventHandler(async (event) => {
  const runtimeConfig = useRuntimeConfig(event)
  const publicConfig = runtimeConfig.public || {}

  const requestUrl = getRequestURL(event)
  const configuredSiteUrl =
    typeof publicConfig.siteUrl === 'string' ? publicConfig.siteUrl.trim() : ''
  const siteUrl = (configuredSiteUrl || `${requestUrl.origin}`)
    .replace(/\/+$/, '')

  const configuredApiBase =
    typeof publicConfig.apiBase === 'string' ? publicConfig.apiBase.trim() : ''
  const apiBase = configuredApiBase.replace(/\/+$/, '')

  const entries: SitemapEntry[] = []
  const seen = new Set<string>()

  const addEntry = (entry: SitemapEntry) => {
    if (!entry.loc || seen.has(entry.loc)) {
      return
    }

    seen.add(entry.loc)
    entries.push(entry)
  }

  const buildUrl = (path: string) => {
    const normalized = path.startsWith('/') ? path : `/${path}`

    try {
      const href = new URL(normalized, `${siteUrl.replace(/\/+$/, '')}/`).href

      if (normalized === '/') {
        return href
      }

      return href.replace(/\/+$/, '')
    }
    catch {
      return `${siteUrl}${normalized}`
    }
  }

  const formatDate = (value?: string | null) => {
    if (!value) {
      return undefined
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
      return undefined
    }

    return date.toISOString()
  }

  const staticPaths = ['/', '/about', '/products', '/our-companies', '/contact', '/privacy']

  for (const path of staticPaths) {
    addEntry({
      loc: buildUrl(path),
      changefreq: path === '/' ? 'daily' : 'weekly',
      priority: path === '/' ? '1.0' : undefined,
    })
  }

  if (apiBase) {
    try {
      let page = 1
      let lastPage = 1

      do {
        const response = await ofetch<ProductIndexResponse>(`${apiBase}/products`, {
          query: {
            per_page: 50,
            page,
          },
        })

        const products = Array.isArray(response?.data) ? response.data : []

        for (const product of products) {
          addEntry({
            loc: buildUrl(`/products/${product.id}`),
            lastmod: formatDate(product.updated_at || product.created_at),
            changefreq: 'weekly',
          })
        }

        const meta = response?.meta
        lastPage = meta?.last_page && meta.last_page > 0 ? meta.last_page : page
        page += 1
      } while (page <= lastPage)
    }
    catch (error) {
      console.error('sitemap: unable to load products', error)
    }

    try {
      const companies = await ofetch<CompanySummary[] | { data?: CompanySummary[] }>(
        `${apiBase}/company-profiles`,
      )

      const records = Array.isArray(companies)
        ? companies
        : Array.isArray(companies?.data)
        ? companies.data
        : []

      for (const company of records) {
        if (!company?.slug) {
          continue
        }

        addEntry({
          loc: buildUrl(`/our-companies/${company.slug}`),
          lastmod: formatDate(company.updated_at),
          changefreq: 'monthly',
        })
      }
    }
    catch (error) {
      console.error('sitemap: unable to load company profiles', error)
    }
  }

  const escapeXml = (value: string) =>
    value
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&apos;')

  const body = [
    '<?xml version="1.0" encoding="UTF-8"?>',
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
    ...entries.map((entry) => {
      const segments = [`  <url>`, `    <loc>${escapeXml(entry.loc)}</loc>`]

      if (entry.lastmod) {
        segments.push(`    <lastmod>${escapeXml(entry.lastmod)}</lastmod>`)
      }

      if (entry.changefreq) {
        segments.push(`    <changefreq>${entry.changefreq}</changefreq>`)
      }

      if (entry.priority) {
        segments.push(`    <priority>${entry.priority}</priority>`)
      }

      segments.push('  </url>')

      return segments.join('\n')
    }),
    '</urlset>',
  ].join('\n')

  setHeader(event, 'Content-Type', 'application/xml')
  setHeader(event, 'Cache-Control', 'public, max-age=300, s-maxage=900')

  return body
})
