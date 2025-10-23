import { defineEventHandler, getRequestURL, setHeader } from 'h3'
import { useRuntimeConfig } from '#imports'

export default defineEventHandler((event) => {
  const runtimeConfig = useRuntimeConfig(event)
  const publicConfig = runtimeConfig.public || {}
  const requestUrl = getRequestURL(event)

  const configuredSiteUrl =
    typeof publicConfig.siteUrl === 'string' ? publicConfig.siteUrl.trim() : ''
  const siteUrl = (configuredSiteUrl || requestUrl.origin).replace(/\/+$/, '')

  const body = [
    'User-agent: *',
    'Allow: /',
    '',
    `Sitemap: ${siteUrl}/sitemap.xml`,
    '',
  ].join('\n')

  setHeader(event, 'Content-Type', 'text/plain; charset=utf-8')
  setHeader(event, 'Cache-Control', 'public, max-age=300, s-maxage=900')

  return body
})
