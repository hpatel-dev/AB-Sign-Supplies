<script setup lang="ts">
interface Product {
  id: number
  name: string
  description?: string | null
  description_html?: string | null
  image_url?: string | null
  is_featured?: boolean
}

interface ApiCollection<T> {
  data: T[]
  meta?: {
    current_page: number
    last_page: number
    total: number
  }
}

const route = useRoute()
const router = useRouter()
const runtimeConfig = useRuntimeConfig()
const requestUrl = useRequestURL()

const search = ref<string>((route.query.search as string) || '')
const currentPage = ref<number>(Number(route.query.page ?? 1) || 1)

const debouncedSearch = useDebounce(search, 300)

watch(
  () => route.query,
  query => {
    const incomingSearch = (query.search as string) ?? ''
    const incomingPage = Number(query.page ?? 1) || 1

    if (incomingSearch !== search.value) {
      search.value = incomingSearch
    }

    if (incomingPage !== currentPage.value) {
      currentPage.value = incomingPage
    }
  },
)

watch(search, () => {
  currentPage.value = 1
})

const queryParams = computed(() => ({
  search: debouncedSearch.value || undefined,
  page: currentPage.value,
  per_page: 12,
}))

watch(
  queryParams,
  params => {
    router.replace({
      query: {
        ...(params.search ? { search: params.search } : {}),
        ...(params.page && params.page !== 1 ? { page: String(params.page) } : {}),
      },
    })
  },
  { deep: true },
)

const productsResponse = useApiFetch<ApiCollection<Product>>('/products', {
  query: queryParams,
  watch: [queryParams],
})

const products = computed<ApiCollection<Product> | null>(() => productsResponse.data.value ?? null)
const pending = productsResponse.pending

const maxPage = computed(() => products.value?.meta?.last_page ?? 1)
const totalResults = computed(() => products.value?.meta?.total ?? null)

const goToPage = (page: number) => {
  if (page < 1 || page > maxPage.value || page === currentPage.value) return
  currentPage.value = page
}

const siteOrigin = computed(() => {
  const configured = typeof runtimeConfig.public?.siteUrl === 'string'
    ? runtimeConfig.public.siteUrl.trim()
    : ''

  if (configured) {
    return configured.replace(/\/+$/, '')
  }

  return requestUrl.origin.replace(/\/+$/, '')
})

const canonicalUrl = computed(() => {
  if (!siteOrigin.value) {
    return null
  }

  const params = new URLSearchParams()

  if (debouncedSearch.value) {
    params.set('search', debouncedSearch.value)
  }

  if (currentPage.value > 1) {
    params.set('page', String(currentPage.value))
  }

  const path = route.path.startsWith('/') ? route.path : `/${route.path}`
  const queryString = params.toString()

  try {
    return new URL(queryString ? `${path}?${queryString}` : path, `${siteOrigin.value}/`).href
  }
  catch {
    return `${siteOrigin.value}${path}`
  }
})

const toAbsoluteUrl = (value?: string | null) => {
  const raw = typeof value === 'string' ? value.trim() : ''

  if (!raw) {
    return null
  }

  if (/^https?:\/\//i.test(raw)) {
    return raw
  }

  if (raw.startsWith('//')) {
    return `${requestUrl.protocol}${raw}`
  }

  try {
    return new URL(raw.startsWith('/') ? raw : `/${raw}`, `${siteOrigin.value}/`).href
  }
  catch {
    return raw
  }
}

const baseDescription =
  'Browse professional signage materials, hardware, lighting, and equipment across our full product catalog.'

const seoDescription = computed(() => {
  if (debouncedSearch.value) {
    return `Browse signage products matching “${debouncedSearch.value}”.`
  }

  if (totalResults.value && totalResults.value > 0) {
    return `Browse ${totalResults.value} signage products, from raw materials to finished hardware, in our complete catalog.`
  }

  return baseDescription
})

const { apply: applySeo } = useSeo({ slug: 'products' })

watch(
  [debouncedSearch, canonicalUrl, totalResults],
  () => {
    const description = seoDescription.value
    const canonical = canonicalUrl.value ?? undefined

    applySeo(
      {
        description,
        canonicalUrl: canonical,
        openGraph: { description },
        twitter: { description },
      },
      { replace: true },
    )
  },
  { immediate: true },
)

const structuredData = computed(() => {
  if (!products.value?.data?.length || !canonicalUrl.value) {
    return null
  }

  const items = products.value.data
    .map((product, index) => {
      if (!product?.name) {
        return null
      }

      const productUrl = toAbsoluteUrl(`/products/${product.id}`)

      if (!productUrl) {
        return null
      }

      const item: Record<string, unknown> = {
        '@type': 'Product',
        name: product.name,
      }

      if (product.description) {
        item.description = product.description
      }

      const imageUrl = toAbsoluteUrl(product.image_url)

      if (imageUrl) {
        item.image = imageUrl
      }

      return {
        '@type': 'ListItem',
        position: (currentPage.value - 1) * (queryParams.value.per_page ?? 12) + index + 1,
        url: productUrl,
        item,
      }
    })
    .filter((entry): entry is Record<string, unknown> => Boolean(entry))

  if (!items.length) {
    return null
  }

  return {
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    url: canonicalUrl.value,
    numberOfItems: items.length,
    itemListOrder: 'https://schema.org/ItemListOrderAscending',
    itemListElement: items,
  }
})

useHead(() => ({
  script: structuredData.value
    ? [
        {
          key: 'ldjson-products-index',
          type: 'application/ld+json',
          children: JSON.stringify(structuredData.value),
        },
      ]
    : [],
}))

</script>

<template>
  <div class="mx-auto w-full max-w-7xl px-6 py-24">
    <SectionHeading title="Products" subtitle="Browse our full catalog" />

    <div class="mt-10 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3">
        <label class="text-sm font-semibold text-secondary/70" for="search">Search</label>
        <input
          id="search"
          v-model="search"
          type="search"
          placeholder="Find vinyl, hardware, lighting, and more"
          class="w-full rounded-md border border-gray-200 bg-white px-4 py-3 text-secondary placeholder:text-secondary/40 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40"
        />
      </div>
    </div>

    <div class="mt-12">
      <p v-if="products?.meta" class="text-sm text-secondary/60">
        Showing page {{ products.meta.current_page }} of {{ products.meta.last_page }} - {{ products.meta.total }} items total
      </p>

      <div v-if="pending" class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <div
          v-for="index in 12"
          :key="index"
          class="animate-pulse rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
        >
          <div class="mb-4 h-40 rounded-lg bg-gray-200" />
          <div class="mb-2 h-6 w-3/4 rounded bg-gray-200" />
          <div class="h-4 w-full rounded bg-gray-100" />
        </div>
      </div>

      <div
        v-else-if="products?.data?.length"
        class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3"
      >
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
      </div>

      <div v-else class="mt-6 rounded-xl border border-gray-200 bg-white p-10 text-center text-secondary/80 shadow-sm">
        <p>No products matched your search. Try a different keyword.</p>
      </div>
    </div>

    <div v-if="maxPage > 1" class="mt-12 flex flex-wrap items-center justify-center gap-4">
      <button
        class="rounded-md border border-gray-200 px-4 py-2 text-sm text-secondary transition hover:border-primary hover:text-primary"
        :disabled="currentPage === 1"
        :class="{ 'cursor-not-allowed opacity-40': currentPage === 1 }"
        type="button"
        @click="goToPage(currentPage - 1)"
      >
        Previous
      </button>
      <div class="flex items-center gap-2 text-sm text-secondary/60">
        Page {{ currentPage }} of {{ maxPage }}
      </div>
      <button
        class="rounded-md border border-gray-200 px-4 py-2 text-sm text-secondary transition hover:border-primary hover:text-primary"
        :disabled="currentPage === maxPage"
        :class="{ 'cursor-not-allowed opacity-40': currentPage === maxPage }"
        type="button"
        @click="goToPage(currentPage + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

