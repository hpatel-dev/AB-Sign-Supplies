<script setup lang="ts">
import { computed } from 'vue'

import { companyProfiles } from '~/data/companies'

const companies = companyProfiles

const companySummary = computed(
  () => `Explore our extended family: ${companies.map(company => company.name).join(', ')}.`,
)

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
    <SectionHeading title="Our Sister Companies" subtitle="Expanded capabilities" />

    <div class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-3">
      <NuxtLink
        v-for="company in companies"
        :key="company.slug"
        :to="`/our-companies/${company.slug}`"
        class="group flex h-full flex-col gap-4 rounded-xl border border-secondary/20 bg-dark/70 p-6 text-left shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:border-primary/50 hover:shadow-primary/30"
      >
        <img :src="company.logo" :alt="company.name" class="h-14 w-14 rounded border border-primary/30 bg-primary/20" />
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
  </div>
</template>
