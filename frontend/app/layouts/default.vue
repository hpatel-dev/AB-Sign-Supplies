<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import type { CompanyInfo } from '~/composables/useCompanyInfo'

const navigationLinks = [
  { name: 'Home', to: '/' },
  { name: 'Products', to: '/products' },
  { name: 'About', to: '/about' },
  { name: 'Our Companies', to: '/our-companies' },
  { name: 'Contact', to: '/contact' },
]

const mobileMenuOpen = ref(false)
const currentYear = new Date().getFullYear()
const route = useRoute()

const companyInfo = useCompanyInfo()
const company = computed<CompanyInfo | null>(() => companyInfo.data.value ?? null)

const siteName = computed(() => company.value?.site_name?.trim() || 'AB Sign Supplies')
const tagline = computed(() => company.value?.tagline?.trim() || null)
const logoUrl = computed(() => company.value?.logo_url ?? '/images/logo.svg')
const contactEmail = computed(() => {
  const email = company.value?.contact_email?.trim()
  return email && /\S+@\S+\.\S+/.test(email) ? email : null
})
const contactPhone = computed(() => {
  const phone = company.value?.contact_phone?.trim()
  return phone && phone.length > 3 ? phone : null
})
const contactPhoneHref = computed(() => {
  if (!contactPhone.value) return null
  return `tel:${contactPhone.value.replace(/[^0-9+]/g, '')}`
})
const address = computed(() => {
  const location = company.value?.address?.trim()
  return location && location.length > 3 ? location : null
})
const primaryCta = computed(() => {
  const cta = company.value?.hero?.primary_cta
  return cta?.label && cta?.url
    ? { label: cta.label, url: cta.url }
    : { label: 'Request a Quote', url: '/contact' }
})
const secondaryCta = computed(() => {
  const cta = company.value?.hero?.secondary_cta
  if (cta?.label && cta?.url) {
    return { label: cta.label, url: cta.url }
  }
  return null
})

watch(
  () => route.fullPath,
  () => {
    mobileMenuOpen.value = false
  },
)
</script>

<template>
  <div class="min-h-screen bg-dark text-secondary">
    <div class="flex min-h-screen flex-col">
      <header class="border-b border-white/10 bg-dark">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
          <NuxtLink to="/" class="flex items-center gap-4">
            <img
              :src="logoUrl"
              :alt="siteName"
              class="h-[100px] w-[100px] rounded-2xl object-contain"
            />
            <div class="text-left">
              <p class="text-lg font-semibold text-secondary">{{ siteName }}</p>
              <p v-if="tagline" class="text-sm text-secondary/70">{{ tagline }}</p>
            </div>
          </NuxtLink>

          <nav class="hidden items-center gap-6 text-sm font-medium text-secondary lg:flex">
            <NuxtLink
              v-for="link in navigationLinks"
              :key="link.to"
              :to="link.to"
              class="nav-link nav-link--desktop inline-flex items-center justify-center relative px-1 transition hover:text-primary"
              active-class="text-primary"
            >
              {{ link.name }}
            </NuxtLink>
          </nav>

          <button
            class="inline-flex items-center justify-center rounded-md border border-secondary/40 bg-dark/60 p-2 text-secondary/80 lg:hidden"
            type="button"
            @click="mobileMenuOpen = !mobileMenuOpen"
            aria-label="Toggle navigation"
          >
            <svg
              v-if="!mobileMenuOpen"
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="m6 18 12-12M6 6l12 12" />
            </svg>
          </button>
        </div>

        <transition name="fade">
          <nav v-if="mobileMenuOpen" class="border-t border-white/10 bg-dark px-6 pb-6 lg:hidden">
            <div class="flex flex-col gap-3 pt-4">
              <NuxtLink
                v-for="link in navigationLinks"
                :key="link.to"
                :to="link.to"
                class="nav-link nav-link--mobile rounded-md px-4 py-3 text-base font-medium text-secondary transition hover:bg-primary/20 hover:text-secondary"
                active-class="bg-primary/30 text-secondary"
              >
                {{ link.name }}
              </NuxtLink>
            </div>
          </nav>
        </transition>
      </header>

      <main class="flex-1 bg-dark">
        <slot />
      </main>

      <footer class="border-t border-white/10 bg-gradient-to-r from-dark via-dark to-secondary/10">
        <div class="mx-auto w-full max-w-7xl px-6 py-12 text-sm text-secondary/80 footer-container">
          <div class="footer-sections grid gap-10 md:grid-cols-3">
            <div class="footer-section space-y-4">
              <div>
                <NuxtLink to="/" class="inline-flex items-center gap-3">
                  <img
                    :src="logoUrl"
                    :alt="siteName"
                    class="h-12 w-12 rounded-xl object-cover ring-1 ring-secondary/10"
                  />
                  <span class="text-base font-semibold text-secondary">{{ siteName }}</span>
                </NuxtLink>
                <p v-if="tagline" class="mt-3 text-secondary/70">
                  {{ tagline }}
                </p>
              </div>
              <div class="flex flex-wrap gap-3">
                <NuxtLink
                  v-if="primaryCta?.label && primaryCta?.url"
                  :to="primaryCta.url"
                  class="footer-cta footer-cta--primary inline-flex items-center rounded-md bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-wide text-dark transition hover:bg-primary/90"
                >
                  {{ primaryCta.label }}
                </NuxtLink>
                <NuxtLink
                  v-if="secondaryCta?.label && secondaryCta?.url"
                  :to="secondaryCta.url"
                  class="footer-cta footer-cta--secondary inline-flex items-center rounded-md border border-secondary/40 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-secondary transition hover:border-primary/40 hover:text-primary"
                >
                  {{ secondaryCta.label }}
                </NuxtLink>
              </div>
            </div>
            <div class="footer-section">
              <p class="text-sm font-semibold uppercase tracking-wide text-secondary">Quick Links</p>
              <ul class="mt-4 space-y-2 text-secondary">
                <li v-for="link in navigationLinks" :key="link.to">
                  <NuxtLink :to="link.to" class="footer-nav-link transition hover:text-primary">
                    {{ link.name }}
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/privacy" class="footer-nav-link transition hover:text-primary">Privacy Policy</NuxtLink>
                </li>
              </ul>
            </div>
            <div class="footer-section">
              <p class="text-sm font-semibold uppercase tracking-wide text-secondary">Contact</p>
              <ul class="mt-4 space-y-3 text-secondary">
                <li v-if="contactPhone && contactPhoneHref" class="flex items-start gap-3">
                  <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-secondary/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.172a1.5 1.5 0 0 0-1.032-1.423l-3.123-1.041a1.5 1.5 0 0 0-1.51.363l-.97.97a11.25 11.25 0 0 1-4.743-4.743l.97-.97a1.5 1.5 0 0 0 .363-1.51l-1.04-3.123A1.5 1.5 0 0 0 9.172 4.5H8a2.25 2.25 0 0 0-2.25 2.25v0Z" />
                    </svg>
                  </span>
                  <a :href="contactPhoneHref" class="transition hover:text-primary">{{ contactPhone }}</a>
                </li>
                <li v-if="contactEmail" class="flex items-start gap-3 break-all">
                  <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-secondary/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.26 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                  </span>
                  <a :href="`mailto:${contactEmail}`" class="transition hover:text-primary">{{ contactEmail }}</a>
                </li>
                <li v-if="address" class="flex items-start gap-3">
                  <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-secondary/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                  </span>
                  <address class="not-italic whitespace-pre-line leading-relaxed">{{ address }}</address>
                </li>
              </ul>
            </div>
          </div>

          <div class="mt-10 flex flex-col gap-4 border-t border-white/10 pt-6 text-xs text-secondary/60 md:flex-row md:items-center md:justify-between">
            <p>&copy; {{ currentYear }} {{ siteName }}. All rights reserved.</p>
            <div class="flex flex-wrap gap-4 text-secondary/70">
              <NuxtLink to="/privacy" class="footer-nav-link transition hover:text-primary">Privacy Policy</NuxtLink>
              <NuxtLink to="/contact" class="footer-nav-link transition hover:text-primary">Support</NuxtLink>
              <a href="/sitemap.xml" class="footer-nav-link transition hover:text-primary" target="_blank" rel="noopener">Sitemap</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes footerFadeUp {
  0% {
    opacity: 0;
    transform: translate3d(0, 1.5rem, 0);
  }
  100% {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}

@keyframes footerLinkGlow {
  0% {
    transform: translate3d(0, 0, 0);
    box-shadow: 0 0 0 0 rgba(218, 215, 205, 0.15);
  }
  100% {
    transform: translate3d(0, -0.15rem, 0);
    box-shadow: 0 10px 20px -12px rgba(218, 215, 205, 0.4);
  }
}

@keyframes footerNavUnderline {
  0% {
    transform: scaleX(0);
    opacity: 0;
  }
  100% {
    transform: scaleX(1);
    opacity: 1;
  }
}

.footer-section {
  opacity: 0;
  transform: translate3d(0, 1.5rem, 0);
  animation: footerFadeUp 0.75s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.footer-sections > .footer-section:nth-child(1) {
  animation-delay: 0.15s;
}

.footer-sections > .footer-section:nth-child(2) {
  animation-delay: 0.3s;
}

.footer-sections > .footer-section:nth-child(3) {
  animation-delay: 0.45s;
}

.footer-container:hover .footer-cta,
.footer-container:hover .footer-nav-link {
  will-change: transform;
}

.footer-cta {
  position: relative;
  transition: transform 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease, color 0.25s ease;
}

.footer-cta--primary:hover,
.footer-cta--primary:focus-visible {
  animation: footerLinkGlow 0.4s ease forwards;
}

.footer-cta--secondary::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: inherit;
  border: 1px solid transparent;
  transition: border-color 0.25s ease;
}

.footer-cta--secondary:hover::after,
.footer-cta--secondary:focus-visible::after {
  border-color: rgba(129, 140, 248, 0.5);
}

.footer-nav-link {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding-bottom: 0.1rem;
  transition: color 0.25s ease, transform 0.25s ease;
}

.footer-nav-link::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -0.35rem;
  height: 2px;
  width: 100%;
  background: currentColor;
  transform: scaleX(0);
  opacity: 0;
  transform-origin: left center;
}

.footer-nav-link:hover::after,
.footer-nav-link:focus-visible::after {
  animation: footerNavUnderline 0.25s ease forwards;
}

.footer-nav-link:hover,
.footer-nav-link:focus-visible {
  transform: translate3d(0, -0.1rem, 0);
}

@media (prefers-reduced-motion: reduce) {
  .footer-section {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  .footer-cta,
  .footer-nav-link {
    transition: none !important;
    animation: none !important;
    transform: none !important;
    box-shadow: none !important;
  }

  .footer-nav-link::after {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }
}

.nav-link--desktop {
  position: relative;
  padding-bottom: 0.25rem;
}

.nav-link--desktop::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -0.35rem;
  width: 100%;
  height: 2px;
  background-color: currentColor;
  transform: scaleX(0);
  transform-origin: left center;
  opacity: 0;
  transition: transform 0.25s ease, opacity 0.25s ease;
}

.nav-link--desktop:hover::after,
.nav-link--desktop.text-primary::after {
  transform: scaleX(1);
  opacity: 1;
}
</style>
