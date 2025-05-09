<template>
  <div>
    <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="[badgeColorClasses]"
    >
      <template v-if="iconClass">
        <svg v-if="iconType === 'heroicon'" class="mr-1.5 h-2 w-2" fill="currentColor" viewBox="0 0 8 8">
          <circle cx="4" cy="4" r="3" />
        </svg>
        <i v-else :class="[iconClass, 'mr-1.5']"></i>
      </template>
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

  return props.column.color || 'gray';
});

const badgeColorClasses = computed(() => {
  const colorMap = {
    gray: 'bg-gray-100 text-gray-800',
    red: 'bg-red-100 text-red-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    green: 'bg-green-100 text-green-800',
    blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    pink: 'bg-pink-100 text-pink-800',

    // Aliases
    primary: 'bg-indigo-100 text-indigo-800',
    secondary: 'bg-gray-100 text-gray-800',
    success: 'bg-green-100 text-green-800',
    danger: 'bg-red-100 text-red-800',
    warning: 'bg-yellow-100 text-yellow-800',
    info: 'bg-blue-100 text-blue-800',
  };

  return colorMap[badgeColor.value] || colorMap.gray;
});

const iconClass = computed(() => {
  if (stateConfig.value?.icon) {
    return stateConfig.value.icon;
  }

  return props.column.icon || null;
});

const iconType = computed(() => {
  const icon = iconClass.value;

  if (!icon) return null;

  // Detect icon type based on prefix
  if (icon.startsWith('fa-') || icon.startsWith('fas ') || icon.startsWith('far ') || icon.startsWith('fal ') || icon.startsWith('fab ')) {
    return 'fontawesome';
  }

  return 'heroicon';
});
</script>