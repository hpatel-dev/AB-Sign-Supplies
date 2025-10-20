<script setup lang="ts">
import { computed, watch } from 'vue'
import { useRoute } from 'vue-router'

interface ProductCategory {
  id: number
  name: string
}

interface ProductSupplier {
  id: number
  name: string
}

interface Product {
  id: number
  name: string
  description?: string | null
  image_url?: string | null
  category?: ProductCategory | null
  supplier?: ProductSupplier | null
  is_featured?: boolean
}

const route = useRoute()
const productId = computed(() => String(route.params.id))

interface ProductApiResponse {
  data: Product
}

const productResponse = useApiFetch<ProductApiResponse>(() => `/products/${productId.value}`, {
  watch: [productId],
  key: computed(() => `product-${productId.value}`),
})

const product = computed<Product | null>(() => productResponse.data.value?.data ?? null)
const pending = productResponse.pending
const error = productResponse.error

const breadcrumb = computed(() => [
  { label: 'Products', to: '/products' },
  { label: product.value?.name ?? 'Product', to: `/products/${productId.value}` },
])

const requestUrl = useRequestURL()
const { apply: applySeo } = useSeo({ slug: 'products.show' })

const summarizeDescription = (content?: string | null, length = 160) => {
  if (!content) {
    return null
  }

  const text = content.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()

  if (!text) {
    return null
  }

  return text.length > length ? `${text.slice(0, length - 1).trim()}...` : text
}

const canonicalFromRoute = () => {
  if (import.meta.client && window?.location) {
    return `${window.location.origin}${route.fullPath}`
  }

  return new URL(route.fullPath, requestUrl.origin).href
}

watch(
  () => product.value,
  (productDetails) => {
    const canonicalUrl = canonicalFromRoute()

    if (!productDetails) {
      applySeo(
        {
          canonicalUrl,
        },
        { replace: true },
      )

      return
    }

    const description = summarizeDescription(productDetails.description)

    applySeo(
      {
        title: productDetails.name,
        description,
        canonicalUrl,
        openGraph: {
          title: productDetails.name,
          description,
          imageUrl: productDetails.image_url ?? null,
        },
        twitter: {
          title: productDetails.name,
          description,
          imageUrl: productDetails.image_url ?? null,
          card: productDetails.image_url ? 'summary_large_image' : 'summary',
        },
      },
      { replace: true },
    )
  },
  { immediate: true },
)
</script>

<template>
  <div class="mx-auto w-full max-w-6xl px-6 py-24">
    <div class="text-sm text-secondary/60">
      <NuxtLink v-for="(crumb, index) in breadcrumb" :key="crumb.label" :to="crumb.to" class="hover:text-primary">
        <span v-if="index > 0" class="px-1 text-secondary/40">/</span>
        {{ crumb.label }}
      </NuxtLink>
    </div>

    <div v-if="pending" class="mt-10 grid gap-10 md:grid-cols-[1fr,1.2fr]">
      <div class="aspect-square rounded-xl bg-gray-200" />
      <div class="space-y-4">
        <div class="h-10 w-2/3 rounded bg-gray-200" />
        <div class="h-4 w-full rounded bg-gray-100" />
        <div class="h-4 w-4/5 rounded bg-gray-100" />
        <div class="h-4 w-3/5 rounded bg-gray-100" />
      </div>
    </div>

    <div v-else-if="product" class="mt-10 grid gap-10 md:grid-cols-[1fr,1.2fr]">
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <img
          :src="product.image_url || '/images/logo.svg'"
          :alt="product.name"
          class="h-full w-full object-cover"
        />
      </div>

      <div class="space-y-8">
        <div>
          <p class="text-xs uppercase tracking-wide text-secondary/60">
            {{ product.category?.name || 'Product' }}
          </p>
          <h1 class="mt-2 text-3xl font-semibold text-secondary">
            {{ product.name }}
          </h1>
          <p v-if="product.is_featured" class="mt-2 inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
            Featured product
          </p>
        </div>

        <div class="prose prose-slate max-w-none text-secondary/80">
          <div v-if="product.description" v-html="product.description" />
          <p v-else>
            Detailed specifications for this product are coming soon. Please reach out through our contact form for assistance.
          </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary/60">Supplier</h2>
          <p class="mt-1 text-lg font-medium text-secondary">
            {{ product.supplier?.name || 'AB Sign Supplies' }}
          </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary/60">Ready to order?</h2>
          <p class="mt-2 text-secondary/70">
            Contact our team for quotes, availability, and recommendations tailored to your signage project.
          </p>
          <NuxtLink
            to="/contact"
            class="mt-4 inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2 text-sm font-semibold text-white transition hover:bg-primary/90"
          >
            Contact Us
          </NuxtLink>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="mt-16 rounded-xl border border-red-200 bg-red-50 p-8 text-red-700">
      We couldn't load this product. Please return to the
      <NuxtLink to="/products" class="underline">product catalog</NuxtLink>.
    </div>
  </div>
</template>

