<template>
  <section class="relative overflow-hidden bg-dark">
    <div class="absolute inset-0 opacity-30">
      <div class="pointer-events-none absolute -top-24 -left-20 h-72 w-72 rounded-full bg-primary blur-3xl" />
      <div class="pointer-events-none absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-primary/60 blur-3xl" />
    </div>
    <div class="relative mx-auto flex w-full max-w-7xl flex-col items-start gap-8 px-6 py-24 lg:flex-row lg:items-center">
      <div class="space-y-6 text-left lg:w-7/12">
        <div class="inline-flex items-center gap-2 rounded-full border border-primary/40 bg-primary/10 px-4 py-1 text-sm font-medium text-primary">
          Trusted by sign makers across North America
        </div>
        <h1 class="text-4xl font-bold text-secondary sm:text-5xl lg:text-6xl">
          Your Complete Source for Signage Supplies
        </h1>
        <p class="max-w-2xl text-lg text-secondary/80">
          AB Sign Supplies partners with leading manufacturers to deliver premium materials, hardware, and equipment for custom signage projects of every size.
        </p>
        <div class="flex flex-wrap gap-4">
          <NuxtLink
            to="/products"
            class="rounded-md bg-primary px-6 py-3 font-semibold text-secondary transition hover:bg-primary/90"
          >
            Shop Products
          </NuxtLink>
          <NuxtLink
            to="/contact"
            class="rounded-md border border-secondary/40 px-6 py-3 font-semibold text-secondary transition hover:border-primary hover:text-primary"
          >
            Request a Quote
          </NuxtLink>
        </div>
      </div>
      <div class="relative w-full max-w-md rounded-xl border border-primary/30 bg-dark/80 p-6 shadow-xl shadow-primary/20">
        <div class="flex items-center gap-3">
          <img src="/images/logo.jpg" alt="AB Sign Supplies" class="h-14 w-14 rounded border border-primary/50 bg-primary/20" />
          <div>
            <p class="text-lg font-semibold text-secondary">Quality. Reliability. Speed.</p>
            <p class="text-sm text-secondary/70">Supplying signage professionals since 1998.</p>
          </div>
        </div>
        <dl class="mt-6 grid gap-4 text-secondary/80">
          <div class="rounded-lg border border-secondary/20 bg-dark/60 p-4">
            <dt class="text-sm text-secondary/60">Product Catalog</dt>
            <dd class="text-2xl font-semibold text-secondary">2,000+ SKUs</dd>
          </div>
          <div class="rounded-lg border border-secondary/20 bg-dark/60 p-4">
            <dt class="text-sm text-secondary/60">Shipping</dt>
            <dd class="text-2xl font-semibold text-secondary">North America</dd>
          </div>
          <div class="rounded-lg border border-secondary/20 bg-dark/60 p-4">
            <dt class="text-sm text-secondary/60">Support</dt>
            <dd class="text-2xl font-semibold text-secondary">Dedicated team</dd>
          </div>
        </dl>
      </div>
    </div>
  </section>
</template>
