<script setup lang="ts">
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

watch(
  () => route.fullPath,
  () => {
    mobileMenuOpen.value = false
  },
)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-dark text-secondary">
    <header class="border-b border-dark/60 bg-dark/95 backdrop-blur">
      <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
        <NuxtLink to="/" class="flex items-center gap-3">
          <img src="/images/logo.png" alt="AB Sign Supplies" class="h-12 w-12 rounded border border-primary/50 bg-primary/10" />
          <div class="text-left">
            <p class="text-lg font-semibold text-secondary">AB Sign Supplies</p>
            <p class="text-sm text-secondary/70">Your complete source for signage supplies</p>
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
        <nav v-if="mobileMenuOpen" class="border-t border-dark/60 bg-dark/98 px-6 pb-6 lg:hidden">
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

    <footer class="mt-12 bg-dark/95">
      <div class="mx-auto w-full max-w-7xl px-6 py-10 text-sm text-secondary/70">
        <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-secondary">AB Sign Supplies</p>
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
