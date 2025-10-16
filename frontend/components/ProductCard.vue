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
  price: number | string
  description?: string | null
  image_url?: string | null
  category?: ProductCategory | null
  supplier?: ProductSupplier | null
  is_featured?: boolean
}

const props = defineProps<{ product: ProductCardProps }>()

const formattedPrice = computed(() => {
  const value = Number(props.product.price ?? 0)
  return value ? new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value) : '—'
})
</script>

<template>
  <article class="group flex h-full flex-col overflow-hidden rounded-xl border border-secondary/20 bg-dark/60 shadow-xl shadow-black/10 transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-primary/30">
    <div class="relative">
      <img
        :src="product.image_url || '/images/logo.png'"
        :alt="product.name"
        class="h-56 w-full object-cover object-center transition group-hover:opacity-90"
        loading="lazy"
      />
      <span
        v-if="product.is_featured"
        class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs font-semibold uppercase tracking-wide text-secondary"
      >
        Featured
      </span>
    </div>
    <div class="flex flex-1 flex-col gap-4 p-6">
      <div>
        <p class="text-xs uppercase tracking-wide text-secondary/60">{{ product.category?.name || 'Product' }}</p>
        <h3 class="mt-1 text-xl font-semibold text-secondary">{{ product.name }}</h3>
      </div>
      <p class="line-clamp-3 text-sm text-secondary/70">
        {{ product.description || 'Versatile signage accessory ready to integrate with your next build.' }}
      </p>
      <div class="mt-auto flex items-end justify-between">
        <p class="text-lg font-semibold text-primary">{{ formattedPrice }}</p>
        <span class="text-xs uppercase tracking-wide text-secondary/50">
          {{ product.supplier?.name || 'AB Sign Supplies' }}
        </span>
      </div>
    </div>
  </article>
</template>
