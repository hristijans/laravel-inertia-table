<!-- ButtonAction.vue -->
<template>
    <button
        class="button-action"
        :class="[`button-${action.color || 'primary'}`, `button-${action.size || 'md'}`]"
        @click="handleClick"
    >
        <i v-if="action.icon" :class="action.icon"></i>
        {{ action.label }}
    </button>
</template>

<script setup>
import { ref } from 'vue';

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

<style>
.button-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    border-radius: 0.375rem;
    cursor: pointer;
}

.button-action i {
    margin-right: 0.5rem;
}

.button-primary {
    background-color: #4299e1;
    color: white;
    border: 1px solid #3182ce;
}

.button-danger {
    background-color: #f56565;
    color: white;
    border: 1px solid #e53e3e;
}

.button-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.button-md {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.button-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}
</style>
