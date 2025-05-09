<template>
  <div>
    <label :for="`filter-${filter.name}`" class="block text-sm font-medium text-gray-700 mb-1">
      {{ filter.label }}
    </label>
    <select
        :id="`filter-${filter.name}`"
        :value="modelValue"
        :multiple="filter.multiple"
        class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
        :class="{ 'h-32': filter.multiple }"
        @change="handleChange"
    >
      <option v-if="!filter.multiple" value="">All</option>
      <option
          v-for="(label, value) in filter.options"
          :key="value"
          :value="value"
          :selected="isSelected(value)"
      >
        {{ label }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  filter: {
    type: Object,
    required: true,
  },
  modelValue: {
    required: true,
  },
});

const emit = defineEmits(['update:modelValue']);

const isSelected = (value) => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(value);
  }

  return props.modelValue === value;
};

const handleChange = (event) => {
  if (props.filter.multiple) {
    const options = event.target.options;
    const selectedValues = [];

    for (let i = 0; i < options.length; i++) {
      if (options[i].selected) {
        selectedValues.push(options[i].value);
      }
    }

    emit('update:modelValue', selectedValues);
  } else {
    emit('update:modelValue', event.target.value);
  }
};
</script>