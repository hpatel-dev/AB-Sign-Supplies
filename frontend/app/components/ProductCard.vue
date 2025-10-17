<script setup lang="ts">
interface ProductCategory {
  id: number
  name: string
}

interface ProductSupplier {
  id: number
  name: string
}

interface ProductCardProps {
  id: number
  name: string
  description?: string | null
  image_url?: string | null
  category?: ProductCategory | null
  supplier?: ProductSupplier | null
  is_featured?: boolean
}

const props = defineProps<{ product: ProductCardProps }>()
</script>

<template>
  <NuxtLink
    :to="`/products/${product.id}`"
    class="group flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-lg"
  >
    <div class="relative">
      <img
        :src="product.image_url || '/images/logo.png'"
        :alt="product.name"
        class="h-56 w-full object-cover object-center transition group-hover:scale-[1.01]"
        loading="lazy"
      />
      <span
        v-if="product.is_featured"
        class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow"
      >
        Featured
      </span>
    </div>
    <div class="flex flex-1 flex-col gap-4 p-6">
      <div>
        <p class="text-xs uppercase tracking-wide text-secondary/60">{{ product.category?.name || 'Product' }}</p>
        <h3 class="mt-1 text-xl font-semibold text-secondary transition group-hover:text-primary">{{ product.name }}</h3>
      </div>
      <p class="line-clamp-3 text-sm text-secondary/70">
        {{ product.description || 'Versatile signage accessory ready to integrate with your next build.' }}
      </p>
      <div class="mt-auto text-xs uppercase tracking-wide text-secondary/50">
        {{ product.supplier?.name || 'AB Sign Supplies' }}
      </div>
    </div>
  </NuxtLink>
</template>
