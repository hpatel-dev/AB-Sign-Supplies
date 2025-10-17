<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

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

interface CompanyInfo {
  site_name?: string | null
  tagline?: string | null
  logo_url?: string | null
}

const { data: companyInfo } = await useApiFetch<CompanyInfo>('/company')

const siteName = computed(() => companyInfo.value?.site_name ?? 'AB Sign Supplies')
const tagline = computed(() => companyInfo.value?.tagline ?? 'Your complete source for signage supplies')
const logoUrl = computed(() => companyInfo.value?.logo_url ?? '/images/logo.png')

watch(
  () => route.fullPath,
  () => {
    mobileMenuOpen.value = false
  },
)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-white text-secondary">
    <header class="border-b border-gray-200 bg-white/90 backdrop-blur">
      <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
        <NuxtLink to="/" class="flex items-center gap-3">
          <img :src="logoUrl" :alt="siteName" class="h-12 w-12 rounded border border-primary/20 bg-primary/10 object-cover" />
          <div class="text-left">
            <p class="text-lg font-semibold text-secondary">{{ siteName }}</p>
            <p class="text-sm text-secondary/70">{{ tagline }}</p>
          </div>
        </NuxtLink>

        <nav class="hidden items-center gap-6 text-sm font-medium text-secondary lg:flex">
          <NuxtLink
            v-for="link in navigationLinks"
            :key="link.to"
            :to="link.to"
            class="relative transition hover:text-primary"
            active-class="text-primary"
          >
            {{ link.name }}
          </NuxtLink>
        </nav>

        <button
          class="inline-flex items-center justify-center rounded-md border border-secondary/40 p-2 text-secondary/80 lg:hidden"
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
        <nav v-if="mobileMenuOpen" class="border-t border-gray-200 bg-white px-6 pb-6 lg:hidden">
          <div class="flex flex-col gap-3 pt-4">
            <NuxtLink
              v-for="link in navigationLinks"
              :key="link.to"
              :to="link.to"
              class="rounded-md px-4 py-3 text-base font-medium text-secondary transition hover:bg-primary/10 hover:text-primary"
              active-class="bg-primary/15 text-primary"
            >
              {{ link.name }}
            </NuxtLink>
          </div>
        </nav>
      </transition>
    </header>

    <main class="flex-1">
      <slot />
    </main>

    <footer class="mt-12 border-t border-gray-200 bg-gray-50">
      <div class="mx-auto w-full max-w-7xl px-6 py-10 text-sm text-secondary/70">
        <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-secondary">{{ siteName }}</p>
            <p>&copy; {{ currentYear }} All rights reserved.</p>
          </div>
          <div class="flex gap-6">
            <NuxtLink to="/privacy" class="transition hover:text-primary">Privacy</NuxtLink>
            <NuxtLink to="/contact" class="transition hover:text-primary">Contact</NuxtLink>
          </div>
        </div>
      </div>
    </footer>
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
</style>
