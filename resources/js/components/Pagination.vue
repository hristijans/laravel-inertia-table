<template>
  <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing <span class="font-medium">{{ from }}</span> to <span class="font-medium">{{ to }}</span> of <span class="font-medium">{{ total }}</span> results
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <template v-for="(link, index) in paginationLinks" :key="index">
            <a
                v-if="link.url && !link.active"
                :href="link.url"
                @click.prevent="navigate(link)"
                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :class="[
                index === 0 ? 'rounded-l-md' : '',
                index === paginationLinks.length - 1 ? 'rounded-r-md' : '',
              ]"
            >
              <span v-if="index === 0">&laquo; Previous</span>
              <span v-else-if="index === paginationLinks.length - 1">Next &raquo;</span>
              <span v-else v-html="link.label"></span>
            </a>
            <span
                v-else-if="link.active"
                class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                :class="[
                index === 0 ? 'rounded-l-md' : '',
                index === paginationLinks.length - 1 ? 'rounded-r-md' : '',
              ]"
            >
              <span v-if="index === 0">&laquo; Previous</span>
              <span v-else-if="index === paginationLinks.length - 1">Next &raquo;</span>
              <span v-else v-html="link.label"></span>
            </span>
            <span
                v-else
                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-400 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 cursor-not-allowed"
                :class="[
                index === 0 ? 'rounded-l-md' : '',
                index === paginationLinks.length - 1 ? 'rounded-r-md' : '',
              ]"
            >
              <span v-if="index === 0">&laquo; Previous</span>
              <span v-else-if="index === paginationLinks.length - 1">Next &raquo;</span>
              <span v-else v-html="link.label"></span>
            </span>
          </template>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3'; // or @inertiajs/inertia-vue if using Inertia v1

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
});

const paginationLinks = computed(() => {
  return props.links.filter(link => link.label !== 'meta');
});

const from = computed(() => {
  const metaLink = props.links.find(link => link.label === 'meta');
  return metaLink ? metaLink.meta.from : 1;
});

const to = computed(() => {
  const metaLink = props.links.find(link => link.label === 'meta');
  return metaLink ? metaLink.meta.to : 1;
});

const total = computed(() => {
  const metaLink = props.links.find(link => link.label === 'meta');
  return metaLink ? metaLink.meta.total : 0;
});

const navigate = (link) => {
  if (link.url && !link.active) {
    router.visit(link.url, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  }
};
</script>