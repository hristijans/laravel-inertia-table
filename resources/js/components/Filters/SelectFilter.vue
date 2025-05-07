<!-- SelectFilter.vue -->
<template>
    <div class="select-filter">
        <label :for="`filter-${filter.name}`" class="select-filter-label">
            {{ filter.label }}
        </label>
        <select
            :id="`filter-${filter.name}`"
            class="select-filter-input"
            :value="modelValue"
            :multiple="filter.multiple"
            @change="handleChange"
        >
            <option v-if="!filter.multiple" value="">All</option>
            <option
                v-for="(label, value) in filter.options"
                :key="value"
                :value="value"
            >
                {{ label }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { ref } from 'vue';

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

<style>
.select-filter {
    display: inline-flex;
    flex-direction: column;
    margin-right: 1rem;
}

.select-filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.select-filter-input {
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background-color: white;
}
</style>
