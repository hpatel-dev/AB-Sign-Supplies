<script setup lang="ts">
import { computed, watch } from 'vue'
import type { CompanyInfo } from '~/composables/useCompanyInfo'

const companyInfo = useCompanyInfo()
const company = computed<CompanyInfo | null>(() => companyInfo.data.value ?? null)
const pending = companyInfo.pending
const { apply: applySeo } = useSeo({ slug: 'about' })

const siteName = computed(() => company.value?.site_name?.trim() || 'AB Sign Supplies')
const subtitle = computed(() => company.value?.tagline?.trim() || undefined)

const summarize = (content?: string | null, length = 160) => {
  if (!content) {
    return null
  }

  const text = content.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()

  if (!text) {
    return null
  }

  return text.length > length ? `${text.slice(0, length - 1).trim()}...` : text
}

watch(
  () => company.value?.about_us,
  (aboutContent) => {
    const description = summarize(aboutContent)

    if (description) {
      applySeo({
        description,
        openGraph: { description },
        twitter: { description },
      })
    }
  },
  { immediate: true },
)
</script>

<template>
  <div class="mx-auto w-full max-w-5xl px-6 py-24">
    <SectionHeading :title="`About ${siteName}`" :subtitle="subtitle" />

    <div v-if="pending" class="mt-10 space-y-4">
      <div class="h-6 w-2/3 animate-pulse rounded bg-gray-200" />
      <div class="h-6 w-full animate-pulse rounded bg-gray-200" />
      <div class="h-6 w-3/4 animate-pulse rounded bg-gray-100" />
    </div>

    <div v-else-if="company" class="mt-10 space-y-8 text-secondary/80">
      <div class="prose prose-slate max-w-none rounded-xl border border-gray-200 bg-white p-8 shadow-sm" v-html="company.about_us" />

      <div class="grid gap-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm md:grid-cols-3">
        <div>
          <p class="text-sm uppercase tracking-wide text-secondary/60">Email</p>
          <p class="mt-1 text-lg font-semibold text-secondary">{{ company.contact_email }}</p>
        </div>
        <div>
          <p class="text-sm uppercase tracking-wide text-secondary/60">Phone</p>
          <p class="mt-1 text-lg font-semibold text-secondary">{{ company.contact_phone }}</p>
        </div>
        <div>
          <p class="text-sm uppercase tracking-wide text-secondary/60">Headquarters</p>
          <p class="mt-1 text-lg font-semibold text-secondary">{{ company.address }}</p>
        </div>
      </div>

      <div v-if="company.google_map_embed" class="map-embed overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <div class="aspect-video" v-html="company.google_map_embed" />
      </div>
    </div>

    <div v-else class="mt-10 rounded-xl border border-gray-200 bg-white p-8 text-secondary/80 shadow-sm">
      <p>Company profile information is being updated. Please check back shortly.</p>
    </div>
  </div>
</template>

<style scoped>
.map-embed :deep(iframe) {
  width: 100%;
  height: 100%;
  border: 0;
}

.map-embed :deep(div) {
  width: 100% !important;
  height: 100% !important;
}
</style>

