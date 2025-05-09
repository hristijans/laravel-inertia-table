<template>
  <button
      type="button"
      @click="handleClick"
      class="inline-flex items-center justify-center rounded-md font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      :class="[buttonClasses]"
  >
    <i v-if="action.icon" :class="[action.icon, 'mr-1']"></i>
    {{ action.label }}
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  action: {
    type: Object,
    required: true,
  },
  record: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['action']);

const buttonColor = computed(() => {
  return props.action.color || 'primary';
});

const buttonSize = computed(() => {
  return props.action.size || 'md';
});

const buttonClasses = computed(() => {
  const colorMap = {
    primary: 'bg-indigo-600 hover:bg-indigo-700 text-white',
    secondary: 'bg-white border border-gray-300 hover:bg-gray-50 text-gray-700',
    success: 'bg-green-600 hover:bg-green-700 text-white',
    danger: 'bg-red-600 hover:bg-red-700 text-white',
    warning: 'bg-yellow-600 hover:bg-yellow-700 text-white',
    info: 'bg-blue-600 hover:bg-blue-700 text-white',
  };

  const sizeMap = {
    xs: 'px-2 py-1 text-xs',
    sm: 'px-2.5 py-1.5 text-sm',
    md: 'px-3 py-2 text-sm',
    lg: 'px-4 py-2 text-base',
    xl: 'px-6 py-3 text-base',
  };

  return [
    colorMap[buttonColor.value] || colorMap.primary,
    sizeMap[buttonSize.value] || sizeMap.md,
  ];
});

const handleClick = () => {
  if (props.action.requiresConfirmation) {
    if (confirm(`Are you sure you want to ${props.action.label.toLowerCase()}?`)) {
      emit('action', props.record);
    }
  } else {
    emit('action', props.record);
  }
};
</script>