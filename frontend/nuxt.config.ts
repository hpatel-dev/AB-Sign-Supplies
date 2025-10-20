// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  srcDir: 'app/',
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  css: ['~/assets/css/tailwind.css'],
  modules: ['@nuxtjs/tailwindcss', '@vueuse/nuxt'],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE ?? 'http://localhost/api',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL ?? 'http://localhost:3000',
      defaultTitle: process.env.NUXT_PUBLIC_DEFAULT_TITLE ?? 'AB Sign Supplies',
      defaultDescription: process.env.NUXT_PUBLIC_DEFAULT_DESCRIPTION
        ?? 'Your complete source for signage supplies, products, and services.',
    },
  },
  app: {
    head: {
      title: 'AB Sign Supplies',
      meta: [
        { name: 'description', content: 'Your complete source for signage supplies, products, and services.' },
        { name: 'theme-color', content: '#FE0002', media: '(prefers-color-scheme: light)' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { charset: 'utf-8' },
        { 'http-equiv': 'X-UA-Compatible', content: 'IE=edge' },

      ],
      link: [
        { rel: 'icon', type: 'image/png', href: '/favicon.png' },
      ],
    },
  },
});
