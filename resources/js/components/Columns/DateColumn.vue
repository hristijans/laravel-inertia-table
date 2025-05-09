<!-- TextColumn.vue -->
<template>
    <div class="text-column">
        <template v-if="hasValue">
          <span>{{ displayValue }}</span>
        </template>
        <span v-else class="text-column-empty">
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
