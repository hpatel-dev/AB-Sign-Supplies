<script setup lang="ts">
import { computed } from 'vue'

const companiesResponse = useCompanies()
const companies = computed(() => {
  const payload = companiesResponse.data.value

  if (Array.isArray(payload)) {
    return payload.filter((company) => company && typeof company.slug === 'string' && company.slug.length > 0)
  }

  if (payload && Array.isArray((payload as any).data)) {
    return (payload as any).data.filter(
      (company: any) => company && typeof company.slug === 'string' && company.slug.length > 0,
    )
  }

  return []
})
const pending = companiesResponse.pending

const companySummary = computed(() => {
  if (!companies.value.length) {
    return 'Explore our extended family of specialist signage companies.'
  }
})

useSeo({
  slug: 'our-companies',
  overrides: {
    description: companySummary,
    openGraph: {
      description: companySummary,
    },
    twitter: {
      description: companySummary,
    },
  },
})
</script>

<template>
  <div class="mx-auto w-full max-w-6xl px-6 py-24">
    <SectionHeading title="Our Companies" subtitle="Expanded capabilities" />

    <div v-if="pending" class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-3">
      <div
        v-for="index in 6"
        :key="index"
        class="flex h-full flex-col gap-4 rounded-xl border border-secondary/20 bg-dark/40 p-6 shadow-inner shadow-black/20"
      >
        <div class="h-14 w-14 rounded bg-primary/10" />
        <div class="space-y-3">
          <div class="h-4 w-2/3 rounded bg-primary/10" />
          <div class="h-3 w-full rounded bg-primary/5" />
          <div class="h-3 w-4/5 rounded bg-primary/5" />
        </div>
        <div class="mt-auto h-3 w-1/3 rounded bg-primary/10" />
      </div>
    </div>

    <div v-else-if="companies.length" class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-3">
      <NuxtLink
        v-for="company in companies"
        :key="company.slug"
        :to="`/our-companies/${encodeURIComponent(company.slug)}`"
        class="group flex h-full flex-col gap-4 rounded-xl border border-secondary/20 bg-dark/70 p-6 text-left shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:border-primary/50 hover:shadow-primary/30"
      >
        <img :src="company.logo_url" :alt="company.name" class="h-14 w-14 rounded border border-primary/30 bg-primary/20" />
        <div class="space-y-3">
          <div class="space-y-1">
            <p v-if="company.tagline" class="text-xs uppercase tracking-wide text-secondary/60">
              {{ company.tagline }}
            </p>
            <h3 class="text-xl font-semibold text-secondary transition group-hover:text-primary">{{ company.name }}</h3>
          </div>
          <p class="text-sm text-secondary/75">
            {{ company.summary }}
          </p>
        </div>
        <span class="mt-auto inline-flex items-center gap-2 text-sm font-semibold text-primary transition group-hover:text-primary/80">
          Learn More
          <span aria-hidden="true">&gt;</span>
        </span>
      </NuxtLink>
    </div>

    <div v-else class="mt-12 rounded-xl border border-secondary/20 bg-dark/40 p-10 text-center text-secondary/70">
      We are updating our company directory. Check back soon!
    </div>
  </div>
</template>
