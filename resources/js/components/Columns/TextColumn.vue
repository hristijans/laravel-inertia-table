<template>
  <div>
    <template v-if="hasValue">
      <span v-if="column.truncate && displayValue.length > column.truncate">
        {{ displayValue.substring(0, column.truncate) }}<span class="text-gray-400">...</span>
      </span>
      <span v-else>{{ displayValue }}</span>
    </template>
    <span v-else class="text-gray-400 italic">
      {{ column.default || '—' }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import get from 'lodash/get';

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  record: {
    type: Object,
    required: true,
  },
});

const rawValue = computed(() => get(props.record, props.column.name));
const hasValue = computed(() => rawValue.value !== null && rawValue.value !== undefined);
const displayValue = computed(() => String(rawValue.value));
</script>