<!-- BadgeColumn.vue -->
<template>
    <div class="badge-column">
    <span
        class="badge"
        :class="[
        `badge-${badgeColor}`,
        { 'badge-with-icon': column.icon || stateConfig?.icon }
      ]"
    >
      <i v-if="column.icon || stateConfig?.icon" :class="stateConfig?.icon || column.icon"></i>
      {{ displayValue }}
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
const displayValue = computed(() => String(rawValue.value));

const stateConfig = computed(() => {
    if (!props.column.states || Object.keys(props.column.states).length === 0) {
        return null;
    }

    return props.column.states[rawValue.value] || null;
});

const badgeColor = computed(() => {
    if (stateConfig.value?.color) {
        return stateConfig.value.color;
    }

    return props.column.color || 'default';
});
</script>

<style>
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25em 0.75em;
    font-size: 0.75em;
    font-weight: 600;
    border-radius: 9999px;
}

.badge-with-icon {
    padding-left: 0.5em;
}

.badge-with-icon i {
    margin-right: 0.25em;
}

.badge-default {
    background-color: #e2e8f0;
    color: #4a5568;
}

.badge-primary {
    background-color: #ebf5ff;
    color: #3182ce;
}

.badge-success {
    background-color: #f0fff4;
    color: #38a169;
}

.badge-danger {
    background-color: #fff5f5;
    color: #e53e3e;
}

.badge-warning {
    background-color: #fffaf0;
    color: #dd6b20;
}
</style>
