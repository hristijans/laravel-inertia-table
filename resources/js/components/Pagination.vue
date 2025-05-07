<template>
    <div v-if="links.length > 3" class="pagination">
        <div class="pagination-links">
            <template v-for="(link, index) in links" :key="index">
                <div
                    v-if="link.url"
                    :class="['pagination-link', { active: link.active }]"
                    @click="navigate(link)"
                >
                    <span v-html="link.label"></span>
                </div>
                <div v-else class="pagination-link disabled">
                    <span v-html="link.label"></span>
                </div>
            </template>
        </div>
        <div class="pagination-info">
            Showing {{ from }} to {{ to }} of {{ total }} results
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

const from = computed(() => {
    const fromLink = props.links.find((link) => link.label === 'meta');
    return fromLink ? fromLink.meta.from : 1;
});

const to = computed(() => {
    const toLink = props.links.find((link) => link.label === 'meta');
    return toLink ? toLink.meta.to : 1;
});

const total = computed(() => {
    const totalLink = props.links.find((link) => link.label === 'meta');
    return totalLink ? totalLink.meta.total : 0;
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

<style>
.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.pagination-links {
    display: flex;
    gap: 0.25rem;
}

.pagination-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    border-radius: 0.25rem;
    background-color: white;
    border: 1px solid #e2e8f0;
    font-size: 0.875rem;
    cursor: pointer;
}

.pagination-link.active {
    background-color: #3182ce;
    color: white;
    border-color: #2c5282;
}

.pagination-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.875rem;
    color: #4a5568;
}
</style>
