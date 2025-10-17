<script setup lang="ts">
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

const search = ref<string>((route.query.search as string) || '')
const selectedCategory = ref<string>((route.query.category as string) || '')
const currentPage = ref<number>(Number(route.query.page ?? 1) || 1)

const debouncedSearch = useDebounce(search, 300)

watch(
  () => route.query,
  query => {
    const incomingSearch = (query.search as string) ?? ''
    const incomingCategory = (query.category as string) ?? ''
    const incomingPage = Number(query.page ?? 1) || 1

    if (incomingSearch !== search.value) {
      search.value = incomingSearch
    }

    if (incomingCategory !== selectedCategory.value) {
      selectedCategory.value = incomingCategory
    }

    if (incomingPage !== currentPage.value) {
      currentPage.value = incomingPage
    }
  },
)

watch([search, selectedCategory], () => {
  currentPage.value = 1
})

const queryParams = computed(() => ({
  search: debouncedSearch.value || undefined,
  category: selectedCategory.value || undefined,
  page: currentPage.value,
  per_page: 12,
}))

watch(
  queryParams,
  params => {
    router.replace({
      query: {
        ...(params.search ? { search: params.search } : {}),
        ...(params.category ? { category: params.category } : {}),
        ...(params.page && params.page !== 1 ? { page: String(params.page) } : {}),
      },
    })
  },
  { deep: true },
)

const { data: categories } = await useApiFetch<ProductCategory[]>('/categories')

const {
  data: products,
  pending,
} = await useApiFetch<ApiCollection<Product>>('/products', {
  query: queryParams,
  watch: [queryParams],
})

const maxPage = computed(() => products.value?.meta?.last_page ?? 1)

const goToPage = (page: number) => {
  if (page < 1 || page > maxPage.value || page === currentPage.value) return
  currentPage.value = page
}
</script>

<template>
  <div class="mx-auto w-full max-w-7xl px-6 py-24">
    <SectionHeading title="Products" subtitle="Browse our full catalog" />

    <div class="mt-10 flex flex-col gap-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:flex-row lg:items-end lg:justify-between">
      <div class="flex flex-1 flex-col gap-3">
        <label class="text-sm font-semibold text-secondary/70" for="search">Search</label>
        <input
          id="search"
          v-model="search"
          type="search"
          placeholder="Find vinyl, hardware, lighting, and more"
          class="w-full rounded-md border border-gray-200 bg-white px-4 py-3 text-secondary placeholder:text-secondary/40 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40"
        />
      </div>
      <div class="flex w-full flex-col gap-3 lg:w-64">
        <label class="text-sm font-semibold text-secondary/70" for="category">Category</label>
        <select
          id="category"
          v-model="selectedCategory"
          class="rounded-md border border-gray-200 bg-white px-4 py-3 text-secondary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40"
        >
          <option value="">All categories</option>
          <option v-for="category in categories || []" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>
    </div>

    <div class="mt-12">
      <p v-if="products?.meta" class="text-sm text-secondary/60">
        Showing page {{ products.meta.current_page }} of {{ products.meta.last_page }} - {{ products.meta.total }} items total
      </p>
      <div v-if="pending" class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <div v-for="index in 12" :key="index" class="animate-pulse rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
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
        <p>No products matched your filters. Try adjusting your search or category.</p>
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
