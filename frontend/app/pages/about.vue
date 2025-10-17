<script setup lang="ts">
import { computed } from 'vue'

interface CompanyInfo {
  site_name?: string | null
  tagline?: string | null
  logo_url?: string | null
  about_us: string
  contact_email: string
  contact_phone: string
  address: string
  google_map_embed?: string | null
}

const { data: company, pending } = await useApiFetch<CompanyInfo>('/company')

const siteName = computed(() => company.value?.site_name ?? 'AB Sign Supplies')
const subtitle = computed(() => company.value?.tagline ?? 'Dedicated to signage professionals')
</script>

<template>
  <div class="mx-auto w-full max-w-5xl px-6 py-24">
    <SectionHeading :title="`About ${siteName}`" :subtitle="subtitle" />

    <div v-if="pending" class="mt-10 space-y-4">
      <div class="h-6 w-2/3 animate-pulse rounded bg-dark/40" />
      <div class="h-6 w-full animate-pulse rounded bg-dark/40" />
      <div class="h-6 w-3/4 animate-pulse rounded bg-dark/40" />
    </div>

    <div v-else-if="company" class="mt-10 space-y-8 text-secondary/80">
      <div class="prose prose-invert max-w-none" v-html="company.about_us" />

      <div class="grid gap-6 rounded-xl border border-secondary/20 bg-dark/70 p-8 md:grid-cols-3">
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

      <div v-if="company.google_map_embed" class="overflow-hidden rounded-xl border border-secondary/20">
        <div class="aspect-video" v-html="company.google_map_embed" />
      </div>
    </div>

    <div v-else class="mt-10 rounded-xl border border-secondary/20 bg-dark/70 p-8 text-secondary/80">
      <p>Company profile information is being updated. Please check back shortly.</p>
    </div>
  </div>
</template>
