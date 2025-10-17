<script setup lang="ts">
interface CompanyInfo {
  contact_email: string;
  contact_phone: string;
  address: string;
  google_map_embed?: string | null;
}

const companyInfo = await useApiFetch<CompanyInfo>('/company');

const formState = reactive({
  name: '',
  email: '',
  message: '',
});

const errors = reactive<Record<string, string[]>>({});
const isSubmitting = ref(false);
const successMessage = ref('');

const runtimeConfig = useRuntimeConfig();

const resetForm = () => {
  formState.name = '';
  formState.email = '';
  formState.message = '';
};

const submitForm = async () => {
  isSubmitting.value = true;
  successMessage.value = '';
  Object.keys(errors).forEach(key => delete errors[key]);

  try {
    await $fetch(`${runtimeConfig.public.apiBase}/contact`, {
      method: 'POST',
      body: { ...formState },
    });

    successMessage.value = 'Thank you! Our team will respond shortly.';
    resetForm();
  }
  catch (error: any) {
    if (error?.response?.status === 422) {
      Object.assign(errors, error.response._data?.errors ?? {});
    }
    else {
      successMessage.value = 'Something went wrong. Please try again or reach us by phone.';
    }
  }
  finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="mx-auto w-full max-w-6xl px-6 py-24">
    <SectionHeading title="Contact Us" subtitle="We’re ready to help" />

    <div class="mt-12 grid gap-10 lg:grid-cols-2">
      <form class="space-y-6 rounded-xl border border-secondary/20 bg-dark/70 p-8 shadow-lg shadow-black/10" @submit.prevent="submitForm">
        <div>
          <label class="block text-sm font-semibold text-secondary/70" for="contact-name">Name</label>
          <input
            id="contact-name"
            v-model="formState.name"
            type="text"
            required
            placeholder="Your name"
            class="mt-2 w-full rounded-md border border-secondary/30 bg-dark/60 px-4 py-3 text-secondary placeholder:text-secondary/40 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/60"
          />
          <p v-if="errors.name" class="mt-2 text-sm text-primary">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-secondary/70" for="contact-email">Email</label>
          <input
            id="contact-email"
            v-model="formState.email"
            type="email"
            required
            placeholder="name@company.com"
            class="mt-2 w-full rounded-md border border-secondary/30 bg-dark/60 px-4 py-3 text-secondary placeholder:text-secondary/40 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/60"
          />
          <p v-if="errors.email" class="mt-2 text-sm text-primary">{{ errors.email[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-secondary/70" for="contact-message">How can we help?</label>
          <textarea
            id="contact-message"
            v-model="formState.message"
            rows="6"
            required
            placeholder="Provide as much detail as possible about your signage needs..."
            class="mt-2 w-full rounded-md border border-secondary/30 bg-dark/60 px-4 py-3 text-secondary placeholder:text-secondary/40 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/60"
          />
          <p v-if="errors.message" class="mt-2 text-sm text-primary">{{ errors.message[0] }}</p>
        </div>

        <div class="flex items-center justify-between">
          <p v-if="successMessage" class="text-sm text-secondary/70">
            {{ successMessage }}
          </p>
          <button
            type="submit"
            class="ml-auto inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 font-semibold text-secondary transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:bg-primary/60"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="h-4 w-4 animate-spin rounded-full border-2 border-secondary border-t-transparent" />
            Submit
          </button>
        </div>
      </form>

      <aside class="space-y-6 rounded-xl border border-secondary/20 bg-dark/60 p-8">
        <h3 class="text-2xl font-semibold text-secondary">Contact Details</h3>
        <p class="text-secondary/75">
          Need immediate assistance? Reach out to our customer success team by phone or email. We respond to most inquiries within one business day.
        </p>

        <div v-if="companyInfo.data" class="space-y-4 text-secondary/80">
          <div>
            <p class="text-sm uppercase tracking-wide text-secondary/60">Email</p>
            <p class="text-lg font-semibold text-secondary">{{ companyInfo.data.contact_email }}</p>
          </div>
          <div>
            <p class="text-sm uppercase tracking-wide text-secondary/60">Phone</p>
            <p class="text-lg font-semibold text-secondary">{{ companyInfo.data.contact_phone }}</p>
          </div>
          <div>
            <p class="text-sm uppercase tracking-wide text-secondary/60">Address</p>
            <p class="text-lg font-semibold text-secondary">{{ companyInfo.data.address }}</p>
          </div>
        </div>

        <div v-if="companyInfo.data?.google_map_embed" class="overflow-hidden rounded-xl border border-secondary/20">
          <div class="aspect-video" v-html="companyInfo.data.google_map_embed" />
        </div>
      </aside>
    </div>
  </div>
</template>
