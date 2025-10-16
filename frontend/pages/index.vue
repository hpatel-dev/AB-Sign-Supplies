<script setup lang="ts">
import type { AsyncData } from 'nuxt/app';

interface ProductCategory {
  id: number;
  name: string;
}

interface ProductSupplier {
  id: number;
  name: string;
}

interface Product {
  id: number;
  name: string;
  price: number;
  description?: string | null;
  image_url?: string | null;
  category?: ProductCategory | null;
  supplier?: ProductSupplier | null;
  is_featured?: boolean;
}

interface ApiCollection<T> {
  data: T[];
  links?: Record<string, unknown>;
  meta?: Record<string, unknown>;
}

const { data: featuredProducts, pending } = await useApiFetch<ApiCollection<Product>>('/products', {
  query: {
    featured: true,
    per_page: 6,
  },
});
</script>

<template>
  <div class="space-y-24 pb-20">
    <HeroBanner />

    <section class="mx-auto w-full max-w-7xl px-6">
      <SectionHeading title="Featured Products" subtitle="Curated by our specialists" />

      <div class="mt-12">
        <div v-if="pending" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          <div v-for="index in 6" :key="index" class="animate-pulse rounded-xl border border-secondary/20 bg-dark/60 p-6">
            <div class="mb-4 h-40 rounded-lg bg-dark/40" />
            <div class="mb-2 h-6 w-3/4 rounded bg-dark/30" />
            <div class="h-4 w-full rounded bg-dark/20" />
          </div>
        </div>

        <div
          v-else-if="featuredProducts?.data?.length"
          class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
        >
          <ProductCard
            v-for="product in featuredProducts.data"
            :key="product.id"
            :product="product"
          />
        </div>

        <div v-else class="rounded-xl border border-secondary/20 bg-dark/60 p-10 text-center text-secondary/80">
          <p>No featured products are currently available. Please check back soon.</p>
        </div>

        <div class="mt-10 flex justify-center">
          <NuxtLink
            to="/products"
            class="rounded-md border border-primary px-6 py-3 text-sm font-semibold uppercase tracking-wide text-primary transition hover:bg-primary/10"
          >
            View All Products
          </NuxtLink>
        </div>
      </div>
    </section>

    <section class="bg-dark/80">
      <div class="mx-auto grid w-full max-w-7xl gap-12 px-6 py-20 lg:grid-cols-3">
        <div class="rounded-xl border border-primary/30 bg-dark/70 p-8 shadow-lg shadow-primary/10">
          <h3 class="text-xl font-semibold text-secondary">Premium Materials</h3>
          <p class="mt-3 text-secondary/70">
            From vinyl and acrylics to electrical components, we source reliable materials from trusted brands.
          </p>
        </div>
        <div class="rounded-xl border border-primary/30 bg-dark/70 p-8 shadow-lg shadow-primary/10">
          <h3 class="text-xl font-semibold text-secondary">Industry Expertise</h3>
          <p class="mt-3 text-secondary/70">
            Lean on our specialists for product recommendations and installation best practices for any project.
          </p>
        </div>
        <div class="rounded-xl border border-primary/30 bg-dark/70 p-8 shadow-lg shadow-primary/10">
          <h3 class="text-xl font-semibold text-secondary">Rapid Fulfillment</h3>
          <p class="mt-3 text-secondary/70">
            Nationwide distribution centers and dedicated logistics ensure you meet tight production schedules.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>
