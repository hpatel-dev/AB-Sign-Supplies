<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useMediaQuery } from '@vueuse/core'
import type { CompanyInfo } from '~/composables/useCompanyInfo'

interface CategorySummary {
  id: number
  name: string
  description?: string | null
}

const companyInfo = useCompanyInfo()
const categoriesResponse = useApiFetch<{ data: CategorySummary[] }>('/categories', {
  key: 'hero-categories',
  default: () => ({ data: [] }),
})

const company = computed<CompanyInfo | null>(() => companyInfo.data.value ?? null)
const categories = computed<CategorySummary[]>(() => categoriesResponse.data.value?.data ?? [])
const topCategories = computed(() => categories.value.slice(0, 4))

const siteName = computed(() => company.value?.site_name?.trim() || 'AB Sign Supplies')
const tagline = computed(() => company.value?.tagline?.trim() || null)
const logoUrl = computed(() => company.value?.logo_url ?? '/images/logo.svg')

const defaultHero = {
  headline: 'Your Complete Source for Signage Supplies',
  subheadline:
    'AB Sign Supplies partners with leading manufacturers to deliver premium materials, hardware, and equipment for custom signage projects of every size.',
  primaryCta: { label: 'Shop Products', url: '/products' },
  secondaryCta: { label: 'Request a Quote', url: '/contact' },
  stats: [
    { value: '2,000+', label: 'Product catalog', icon: 'box' },
    { value: 'North America', label: 'Shipping', icon: 'globe' },
    { value: 'Dedicated team', label: 'Support', icon: 'headset' },
  ],
}

const fallbackIcons = ['box', 'globe', 'headset', 'bolt', 'star']

const hero = computed(() => {
  const raw = company.value?.hero

  const rawStats = raw?.stats && raw.stats.length ? raw.stats : defaultHero.stats

  return {
    headline: raw?.headline?.trim() || defaultHero.headline,
    subheadline: raw?.subheadline?.trim() || defaultHero.subheadline,
    background: raw?.background ?? null,
    primaryCta: {
      label: raw?.primary_cta?.label?.trim() || defaultHero.primaryCta.label,
      url: raw?.primary_cta?.url?.trim() || defaultHero.primaryCta.url,
    },
    secondaryCta: {
      label: raw?.secondary_cta?.label?.trim() || defaultHero.secondaryCta.label,
      url: raw?.secondary_cta?.url?.trim() || defaultHero.secondaryCta.url,
    },
    stats: rawStats
      .filter((item) => (item?.value ?? item?.label))
      .map((item, index) => {
        const value = item?.value ? String(item.value).trim() : ''
        const label = item?.label ? String(item.label).trim() : ''
        const icon = item?.icon && typeof item.icon === 'string' && item.icon.trim()
          ? item.icon.trim()
          : fallbackIcons[index] ?? 'star'

        return {
          value: value || label,
          label: label || value,
          icon,
        }
      }),
  }
})

const showCategoryMenu = ref(false)
const categoryMenuRef = ref<HTMLElement | null>(null)
const textAnimationActive = ref(false)
const prefersReducedMotion = useMediaQuery('(prefers-reduced-motion: reduce)')
const heroBackground = computed(() => hero.value.background ?? null)

useHead(() => {
  const background = heroBackground.value

  if (!background?.url) {
    return {
      link: [],
    }
  }

  const link = {
    key: 'hero-background-preload',
    rel: 'preload',
    href: background.url,
    as: background.type === 'video' ? 'video' : 'image',
  } as Record<string, string>

  if (background.type !== 'video') {
    link.fetchpriority = 'high'
  }

  return {
    link: [link],
  }
})

const toggleCategoryMenu = () => {
  if (!topCategories.value.length) {
    return
  }

  showCategoryMenu.value = !showCategoryMenu.value
}

const handleDocumentClick = (event: MouseEvent) => {
  if (!categoryMenuRef.value) {
    return
  }

  if (!categoryMenuRef.value.contains(event.target as Node)) {
    showCategoryMenu.value = false
  }
}

const triggerTextAnimation = () => {
  if (!process.client || prefersReducedMotion.value) {
    textAnimationActive.value = false
    return
  }

  textAnimationActive.value = false

  requestAnimationFrame(() => {
    textAnimationActive.value = true
  })
}

onMounted(() => {
  document.addEventListener('click', handleDocumentClick)
  triggerTextAnimation()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick)
  textAnimationActive.value = false
})

if (process.client) {
  watch(
    () => [
      hero.value.headline,
      hero.value.subheadline,
      hero.value.primaryCta.label,
      hero.value.secondaryCta.label,
    ],
    () => triggerTextAnimation()
  )

  watch(
    prefersReducedMotion,
    (shouldReduce) => {
      if (shouldReduce) {
        textAnimationActive.value = false
      } else {
        triggerTextAnimation()
      }
    },
    { immediate: true },
  )
}

const statIconPath = (icon: string) => {
  switch (icon) {
    case 'box':
      return 'M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z M4 7.5l8 4.5 8-4.5'
    case 'globe':
      return 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Zm0 0c2 2.5 2 12.5 0 15M4.5 9h15M4.5 15h15'
    case 'headset':
      return 'M5 13v-1a7 7 0 0 1 14 0v1m-14 0v4a2 2 0 0 0 2 2h2v-6H7a2 2 0 0 0-2 2Zm14 0v4a2 2 0 0 1-2 2h-2v-6h2a2 2 0 0 1 2 2Z'
    case 'bolt':
      return 'M13 3 6 13h5l-1 8 7-10h-5Z'
    case 'certificate':
      return 'M12 3a7 7 0 1 0 7 7 7 7 0 0 0-7-7Zm0 4v6m-3-3h6m-9 9 3-2 3 2 3-2 3 2'
    case 'star':
    default:
      return 'M12 4.5 13.9 9l4.6.4-3.5 3 1 4.6-4-2.4-4 2.4 1-4.6-3.5-3 4.6-.4Z'
  }
}
</script>

<template>
  <section class="relative overflow-hidden bg-dark">
    <div
      v-if="hero.background && hero.background.type === 'image'"
      class="absolute inset-0"
    >
      <img :src="hero.background.url" alt="Hero background" class="h-full w-full object-cover opacity-40" />
      <div class="absolute inset-0 bg-dark/80" />
    </div>

    <div
      v-else
      class="pointer-events-none absolute inset-0"
    >
      <div class="animate-heroPulse absolute -top-32 left-1/3 h-96 w-96 rounded-full bg-primary/20 blur-3xl" />
      <div class="animate-heroPulseDelay absolute -bottom-40 right-1/4 h-[28rem] w-[28rem] rounded-full bg-secondary/20 blur-3xl" />
    </div>

    <div class="relative mx-auto flex w-full max-w-7xl flex-col items-start gap-10 px-6 py-24 lg:flex-row lg:items-center">
      <div class="space-y-6 text-left lg:w-7/12">
        <div
          class="inline-flex items-center gap-2 rounded-full border border-primary/40 bg-primary/10 px-4 py-1 text-sm font-medium text-primary"
          :class="{ 'hero-text-enter': textAnimationActive }"
        >
          Trusted by sign makers across North America
        </div>
        <h1
          class="text-4xl font-bold text-secondary sm:text-5xl lg:text-6xl"
          :class="{ 'hero-text-enter': textAnimationActive }"
          :style="textAnimationActive ? { animationDelay: '0.1s' } : undefined"
        >
          {{ hero.headline }}
        </h1>
        <p
          class="max-w-2xl text-lg text-secondary/80"
          :class="{ 'hero-text-enter': textAnimationActive }"
          :style="textAnimationActive ? { animationDelay: '0.2s' } : undefined"
        >
          {{ hero.subheadline }}
        </p>
        <div
          class="flex flex-wrap items-center gap-4"
          :class="{ 'hero-text-enter': textAnimationActive }"
          :style="textAnimationActive ? { animationDelay: '0.3s' } : undefined"
        >
          <NuxtLink
            :to="hero.primaryCta.url"
            class="rounded-md bg-primary px-6 py-3 font-semibold text-secondary transition hover:bg-primary/90"
          >
            {{ hero.primaryCta.label }}
          </NuxtLink>
          <div v-if="hero.secondaryCta.label && topCategories.length" class="relative" ref="categoryMenuRef">
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-md border border-secondary/40 px-6 py-3 text-sm font-semibold text-secondary transition hover:border-primary hover:text-primary"
              @click.stop="toggleCategoryMenu"
            >
              {{ hero.secondaryCta.label }}
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 transition-transform"
                :class="{ 'rotate-180': showCategoryMenu }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
              </svg>
            </button>
            <transition name="fade">
              <div
                v-if="showCategoryMenu && topCategories.length"
                class="absolute z-20 mt-2 w-56 rounded-lg border border-secondary/30 bg-dark/95 p-3 text-sm text-secondary shadow-lg shadow-primary/20"
              >
                <NuxtLink
                  v-for="category in topCategories"
                  :key="category.id"
                  :to="{ path: '/products', query: { category: category.id } }"
                  class="block rounded-md px-3 py-2 hover:bg-primary/20 hover:text-secondary"
                  @click="showCategoryMenu = false"
                >
                  {{ category.name }}
                </NuxtLink>
                <NuxtLink
                  class="mt-2 block rounded-md bg-primary/10 px-3 py-2 text-center font-semibold text-primary hover:bg-primary/20"
                  to="/products"
                  @click="showCategoryMenu = false"
                >
                  View all products
                </NuxtLink>
              </div>
            </transition>
          </div>
          <NuxtLink
            v-else-if="hero.secondaryCta.label"
            :to="hero.secondaryCta.url"
            class="rounded-md border border-secondary/40 px-6 py-3 text-sm font-semibold text-secondary transition hover:border-primary hover:text-primary"
          >
            {{ hero.secondaryCta.label }}
          </NuxtLink>
        </div>
      </div>

      <div class="relative w-full max-w-md overflow-hidden rounded-xl border border-primary/30 bg-dark/80 p-6 shadow-xl shadow-primary/20">
        <template v-if="hero.background && hero.background.type === 'video'">
          <video
            :src="hero.background.url"
            autoplay
            loop
            muted
            playsinline
            class="absolute inset-0 h-full w-full object-cover opacity-60"
          />
          <div class="absolute inset-0 bg-dark/80" />
        </template>

        <div class="relative flex items-center gap-3">
          <img :src="logoUrl" :alt="siteName" class="h-14 w-14 rounded border border-primary/50 bg-primary/20 object-cover" />
          <div>
            <p class="text-lg font-semibold text-secondary">{{ siteName }}</p>
            <p v-if="tagline" class="text-sm text-secondary/70">{{ tagline }}</p>
          </div>
        </div>
        <dl class="relative mt-6 grid gap-4 text-secondary/80">
          <div
            v-for="stat in hero.stats"
            :key="`${stat.value}-${stat.label}-${stat.icon}`"
            class="flex items-start gap-4 rounded-lg border border-secondary/20 bg-dark/70 p-4"
          >
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-primary/40 bg-primary/10">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-primary"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path :d="statIconPath(stat.icon)" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div>
              <dt class="text-xs uppercase tracking-wide text-secondary/60">{{ stat.label }}</dt>
              <dd class="text-2xl font-semibold text-secondary">{{ stat.value }}</dd>
            </div>
          </div>
        </dl>
      </div>
    </div>
  </section>
</template>

<style scoped>
@keyframes heroTextSlideIn {
  0% {
    opacity: 0;
    transform: translate3d(3rem, 0, 0);
  }
  100% {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}

.hero-text-enter {
  animation: heroTextSlideIn 0.75s cubic-bezier(0.2, 0.8, 0.2, 1) both;
}

@media (prefers-reduced-motion: reduce) {
  .hero-text-enter {
    animation: none !important;
  }

  .animate-heroPulse,
  .animate-heroPulseDelay,
  .heroPulse {
    animation: none !important;
  }
}

@keyframes heroPulse {
  0%,
  100% {
    transform: scale(1) translate3d(0, 0, 0);
    opacity: 0.4;
  }
  50% {
    transform: scale(1.1) translate3d(0, 0, 0);
    opacity: 0.6;
  }
}

.heroPulse {
  animation: heroPulse 18s ease-in-out infinite;
}

@keyframes heroPulseDelay {
  0%,
  100% {
    transform: scale(1.05) translate3d(0, 0, 0);
    opacity: 0.35;
  }
  50% {
    transform: scale(1.2) translate3d(0, 0, 0);
    opacity: 0.55;
  }
}

.animate-heroPulse {
  animation: heroPulse 20s ease-in-out infinite;
}

.animate-heroPulseDelay {
  animation: heroPulseDelay 24s ease-in-out infinite;
}
</style>



