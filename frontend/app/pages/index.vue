<script setup lang="ts">
import { computed, watch } from 'vue';
import { useCompanyInfo } from '~/composables/useCompanyInfo';

interface Product {
  id: number;
  name: string;
  description?: string | null;
  description_html?: string | null;
  image_url?: string | null;
  is_featured?: boolean;
}

interface ApiCollection<T> {
  data: T[];
  links?: Record<string, unknown>;
  meta?: Record<string, unknown>;
}

const featuredProductsResponse = useApiFetch<ApiCollection<Product>>('/products', {
  query: {
    featured: true,
    per_page: 6,
  },
});

const featuredProducts = computed<ApiCollection<Product> | null>(() => featuredProductsResponse.data.value ?? null);
const pending = featuredProductsResponse.pending;
const productList = computed(() => featuredProducts.value?.data ?? []);

const { apply: applySeo } = useSeo({ slug: 'home' });
const companyInfo = useCompanyInfo();
const runtimeConfig = useRuntimeConfig();
const requestUrl = useRequestURL();

const siteOrigin = computed(() => {
  const configured = typeof runtimeConfig.public?.siteUrl === 'string'
    ? runtimeConfig.public.siteUrl.trim()
    : '';

  if (configured) {
    return configured.replace(/\/+$/, '');
  }

  return requestUrl.origin.replace(/\/+$/, '');
});

const toAbsoluteUrl = (value?: string | null) => {
  const raw = typeof value === 'string' ? value.trim() : '';

  if (!raw) {
    return null;
  }

  if (/^https?:\/\//i.test(raw)) {
    return raw;
  }

  if (raw.startsWith('//')) {
    return `${requestUrl.protocol}${raw}`;
  }

  const base = siteOrigin.value || `${requestUrl.protocol}//${requestUrl.host}`;

  try {
    return new URL(raw.startsWith('/') ? raw : `/${raw}`, base).href;
  }
  catch {
    return raw;
  }
};

const organizationStructuredData = computed(() => {
  const info = companyInfo.data.value;
  const url = siteOrigin.value;

  if (!info || !url) {
    return null;
  }

  const name = info.site_name?.trim() || 'AB Sign Supplies';
  const logo = toAbsoluteUrl(info.logo_url);
  const contactPhone = info.contact_phone?.trim();
  const contactEmail = info.contact_email?.trim();
  const address = info.address?.trim();

  const payload: Record<string, unknown> = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name,
    url,
  };

  if (logo) {
    payload.logo = logo;
  }

  if (contactEmail) {
    payload.email = contactEmail;
  }

  if (contactPhone) {
    payload.contactPoint = [
      {
        '@type': 'ContactPoint',
        telephone: contactPhone,
        contactType: 'sales',
        availableLanguage: 'English',
      },
    ];
  }

  if (address) {
    payload.address = {
      '@type': 'PostalAddress',
      streetAddress: address,
    };
  }

  if (info.tagline?.trim()) {
    payload.slogan = info.tagline.trim();
  }

  return payload;
});

const productStructuredData = computed(() => {
  if (!productList.value.length) {
    return null;
  }

  const items = productList.value
    .filter((product) => product?.name)
    .map((product, index) => {
      const productUrl = toAbsoluteUrl(`/products/${product.id}`);

      if (!productUrl) {
        return null;
      }

      const item: Record<string, unknown> = {
        '@type': 'Product',
        name: product.name,
      };

      if (product.description) {
        item.description = product.description;
      }

      const image = toAbsoluteUrl(product.image_url);

      if (image) {
        item.image = image;
      }

      const brandName = companyInfo.data.value?.site_name?.trim();

      if (brandName) {
        item.brand = {
          '@type': 'Brand',
          name: brandName,
        };
      }

      return {
        '@type': 'ListItem',
        position: index + 1,
        url: productUrl,
        item,
      };
    })
    .filter((item): item is Record<string, unknown> => Boolean(item));

  const listUrl = toAbsoluteUrl('/products');

  if (!items.length || !listUrl) {
    return null;
  }

  return {
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    url: listUrl,
    numberOfItems: items.length,
    itemListElement: items,
  };
});

const structuredDataScripts = computed(() => {
  const scripts = [];
  const organization = organizationStructuredData.value;
  const products = productStructuredData.value;

  if (organization) {
    scripts.push({
      key: 'ldjson-organization',
      type: 'application/ld+json',
      children: JSON.stringify(organization),
    });
  }

  if (products && products.itemListElement?.length) {
    scripts.push({
      key: 'ldjson-product-list',
      type: 'application/ld+json',
      children: JSON.stringify(products),
    });
  }

  return scripts;
});

useHead(() => ({
  script: structuredDataScripts.value,
}));

watch(
  productList,
  (products) => {
    const productWithImage = products.find((item) => item.image_url);

    if (productWithImage?.image_url) {
      applySeo({
        openGraph: {
          imageUrl: productWithImage.image_url,
        },
        twitter: {
          imageUrl: productWithImage.image_url,
          card: 'summary_large_image',
        },
      });
    }
  },
  { immediate: true },
);
</script>

<template>
  <div class="space-y-24 pb-20">
    <HeroBanner />

    <section class="relative bg-white py-20">
      <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary via-dark to-primary" />
      <div class="mx-auto w-full max-w-7xl px-6">
        <SectionHeading title="Featured Products" subtitle="Curated by our specialists" />

        <div class="mt-12">
          <div v-if="pending" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div
              v-for="index in 6"
              :key="index"
              class="animate-pulse rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
            >
              <div class="mb-4 h-40 rounded-lg bg-gray-200" />
              <div class="mb-2 h-6 w-3/4 rounded bg-gray-200" />
              <div class="h-4 w-full rounded bg-gray-100" />
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

          <div v-else class="rounded-xl border border-gray-200 bg-white p-10 text-center text-secondary/80 shadow-sm">
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
      </div>
      <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-primary via-dark to-primary" />
    </section>

    <section class="bg-secondary">
      <div class="mx-auto grid w-full max-w-7xl gap-12 px-6 py-20 lg:grid-cols-3">
        <div class="rounded-xl border border-primary/30 bg-white p-8 shadow-lg shadow-primary/10">
          <h3 class="text-xl font-semibold text-secondary">Premium Materials</h3>
          <p class="mt-3 text-secondary/70">
            From vinyl and acrylics to electrical components, we source reliable materials from trusted brands.
          </p>
        </div>
        <div class="rounded-xl border border-primary/30 bg-white p-8 shadow-lg shadow-primary/10">
          <h3 class="text-xl font-semibold text-secondary">Industry Expertise</h3>
          <p class="mt-3 text-secondary/70">
            Lean on our specialists for product recommendations and installation best practices for any project.
          </p>
        </div>
        <div class="rounded-xl border border-primary/30 bg-white p-8 shadow-lg shadow-primary/10">
          <h3 class="text-xl font-semibold text-secondary">Rapid Fulfillment</h3>
          <p class="mt-3 text-secondary/70">
            Nationwide distribution centers and dedicated logistics ensure you meet tight production schedules.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>





