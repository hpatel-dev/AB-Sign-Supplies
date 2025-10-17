<script setup lang="ts">
defineProps<{ title: string; subtitle?: string }>()
</script>

<template>
  <div class="space-y-2 text-center">
    <p v-if="subtitle" class="text-sm uppercase tracking-[0.3em] text-primary/80">{{ subtitle }}</p>
    <h2 class="text-3xl font-bold text-secondary sm:text-4xl">{{ title }}</h2>
  </div>
</template>
