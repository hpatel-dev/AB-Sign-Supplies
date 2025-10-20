<script setup lang="ts">
import { computed, watchEffect } from 'vue'
import { useRoute } from 'vue-router'
import { createError } from '#imports'

import { getCompanyBySlug } from '~/data/companies'

type ContactItem = {
  label: string;
  value: string;
  href?: string;
  external?: boolean;
}

const route = useRoute()

const profile = computed(() => {
  const slug = String(route.params.slug)
  const match = getCompanyBySlug(slug)

  if (!match) {
    throw createError({ statusCode: 404, statusMessage: 'Company not found' })
  }

  return match
})

const contactItems = computed<ContactItem[]>(() => {
  const contact = profile.value.contact
  const items: ContactItem[] = []

  if (contact.phone) {
    const sanitized = contact.phone.replace(/[^0-9+]/g, '')
    items.push({
      label: 'Phone',
      value: contact.phone,
      href: sanitized ? `tel:${sanitized}` : undefined,
    })
  }

  if (contact.email) {
    items.push({
      label: 'Email',
      value: contact.email,
      href: `mailto:${contact.email}`,
    })
  }

  if (contact.address) {
    items.push({
      label: 'Address',
      value: contact.address,
    })
  }

  if (contact.website) {
    items.push({
      label: 'Website',
      value: contact.website,
      href: contact.website,
      external: true,
    })
  }

  return items
})

const { apply: applySeo } = useSeo({
  slug: computed(() => `our-companies.${profile.value.slug}`),
})

watchEffect(() => {
  const company = profile.value

  applySeo(
    {
      title: `${company.name} | AB Sign Supplies`,
      description: company.summary,
      openGraph: {
        title: company.name,
        description: company.summary,
      },
      twitter: {
        title: company.name,
        description: company.summary,
      },
    },
    { replace: true },
  )
})
</script>

<template>
  <div class="mx-auto w-full max-w-6xl px-6 py-24">
    <NuxtLink to="/our-companies" class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:text-primary/80">
      <span aria-hidden="true">&larr;</span>
      Back to companies
    </NuxtLink>

    <div class="mt-6 rounded-xl border border-secondary/20 bg-white p-8 shadow-sm">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
        <img :src="profile.logo" :alt="profile.name" class="h-20 w-20 rounded border border-primary/30 bg-primary/10 object-cover" />
        <div class="space-y-3 text-secondary">
          <p v-if="profile.tagline" class="text-xs uppercase tracking-wide text-secondary/60">
            {{ profile.tagline }}
          </p>
          <h1 class="text-3xl font-semibold text-secondary">{{ profile.name }}</h1>
          <p class="text-secondary/70">
            {{ profile.summary }}
          </p>
        </div>
      </div>
    </div>

    <div class="mt-12 grid gap-12 lg:grid-cols-[2fr,1fr]">
      <div class="space-y-12">
        <section class="space-y-4">
          <h2 class="text-2xl font-semibold text-secondary">Overview</h2>
          <p class="text-secondary/75">
            {{ profile.overview }}
          </p>
        </section>

        <section class="space-y-6">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-2xl font-semibold text-secondary">Services</h2>
          </div>
          <div class="grid gap-6 md:grid-cols-2">
            <article
              v-for="service in profile.services"
              :key="service.title"
              class="flex h-full flex-col gap-3 rounded-xl border border-secondary/20 bg-white p-6 shadow-sm"
            >
              <h3 class="text-lg font-semibold text-secondary">{{ service.title }}</h3>
              <p class="text-sm text-secondary/70">
                {{ service.description }}
              </p>
            </article>
          </div>
        </section>
      </div>

      <aside>
        <div class="rounded-xl border border-secondary/20 bg-gray-50 p-6 shadow-sm">
          <h2 class="text-xl font-semibold text-secondary">Contact Information</h2>
          <ul class="mt-6 space-y-4">
            <li v-for="item in contactItems" :key="item.label" class="space-y-1">
              <p class="text-xs uppercase tracking-wide text-secondary/60">{{ item.label }}</p>
              <template v-if="item.href">
                <a
                  :href="item.href"
                  class="text-sm font-medium text-primary transition hover:text-primary/80"
                  :target="item.external ? '_blank' : undefined"
                  :rel="item.external ? 'noopener' : undefined"
                >
                  {{ item.value }}
                </a>
              </template>
              <template v-else>
                <p class="text-sm font-medium text-secondary">{{ item.value }}</p>
              </template>
            </li>
          </ul>
        </div>
      </aside>
    </div>
  </div>
</template>
