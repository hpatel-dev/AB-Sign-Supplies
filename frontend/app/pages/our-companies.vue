<script setup lang="ts">
interface CompanyCard {
  name: string
  description: string
  website: string
  logo?: string
}

const sisterCompanies: CompanyCard[] = [
  {
    name: 'AB Architectural Graphics',
    description: 'Specialists in architectural wayfinding systems and ADA-compliant signage for healthcare and corporate campuses.',
    website: 'https://example.com/architectural',
    logo: '/images/logo.png',
  },
  {
    name: 'AB Lighting Solutions',
    description: 'LED illumination, power supplies, and lighting design consultation for interior and exterior signage.',
    website: 'https://example.com/lighting',
    logo: '/images/logo.png',
  },
  {
    name: 'AB Install Services',
    description: 'Nationwide installation network providing site surveys, permitting, and field services for retail roll-outs.',
    website: 'https://example.com/install',
    logo: '/images/logo.png',
  },
]
</script>

<template>
  <div class="mx-auto w-full max-w-6xl px-6 py-24">
    <SectionHeading title="Our Sister Companies" subtitle="Expanded capabilities" />

    <div class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-3">
      <article
        v-for="company in sisterCompanies"
        :key="company.name"
        class="flex h-full flex-col gap-4 rounded-xl border border-secondary/20 bg-dark/70 p-6 shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:border-primary/50 hover:shadow-primary/30"
      >
        <img :src="company.logo" :alt="company.name" class="h-14 w-14 rounded border border-primary/30 bg-primary/20" />
        <div class="space-y-3">
          <h3 class="text-xl font-semibold text-secondary">{{ company.name }}</h3>
          <p class="text-sm text-secondary/75">
            {{ company.description }}
          </p>
        </div>
        <NuxtLink
          :to="company.website"
          target="_blank"
          rel="noopener"
          class="mt-auto inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:text-primary/80"
        >
          Visit Website
          <span aria-hidden="true">→</span>
        </NuxtLink>
      </article>
    </div>
  </div>
</template>
