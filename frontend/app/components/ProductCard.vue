<script setup lang="ts">
import { computed } from 'vue'

interface ProductCardProps {
  id: number
  name: string
  description?: string | null
  description_html?: string | null
  image_url?: string | null
  is_featured?: boolean
}

const props = defineProps<{ product: ProductCardProps }>()

const productLink = computed(() => ({
  name: 'products-id',
  params: { id: String(props.product.id) },
}))
</script>

<template>
  <NuxtLink
    :to="productLink"
    class="group flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-lg"
  >
    <div class="relative">
      <img
        :src="product.image_url || '/images/logo.svg'"
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
        <p class="text-xs uppercase tracking-wide text-secondary/60">Product</p>
        <h3 class="mt-1 text-xl font-semibold text-secondary transition group-hover:text-primary">
          {{ product.name }}
        </h3>
      </div>
      <p class="line-clamp-3 text-sm text-secondary/70">
        {{ product.description || 'Versatile signage accessory ready to integrate with your next build.' }}
      </p>
      <div class="mt-auto text-xs uppercase tracking-wide text-secondary/50">
        View detail
      </div>
    </div>
  </NuxtLink>
</template>
