<template>
    <div class="inertia-table">
        <!-- Filters -->
        <div v-if="filters.length > 0" class="inertia-table-filters">
            <component
                v-for="filter in filters"
                :key="filter.name"
                :is="getFilterComponent(filter.type)"
                :filter="filter"
                :model-value="activeFilters[filter.name] || filter.default"
                @update:model-value="updateFilter(filter.name, $event)"
            />
        </div>

        <!-- Search -->
        <div v-if="searchable.length > 0" class="inertia-table-search">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search..."
                class="inertia-table-search-input"
                @input="debounceSearch"
            />
        </div>

        <!-- Table -->
        <table class="inertia-table-table">
            <thead>
            <tr>
                <th v-for="column in columns" :key="column.name">
                    <div class="inertia-table-header">
                        {{ column.label }}
                        <button
                            v-if="column.sortable"
                            class="inertia-table-sort"
                            @click="toggleSort(column.name)"
                        >
                            <span :class="getSortIcon(column.name)"></span>
                        </button>
                    </div>
                </th>
                <th v-if="actions.length > 0">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(record, index) in records.data" :key="index">
                <td v-for="column in columns" :key="column.name">
                    <component
                        :is="getColumnComponent(column.type)"
                        :column="column"
                        :record="record"
                    />
                </td>
                <td v-if="actions.length > 0" class="inertia-table-actions">
                    <component
                        v-for="action in actions"
                        :key="action.name"
                        :is="getActionComponent(action.type)"
                        :action="action"
                        :record="record"
                        @action="handleAction(action, record)"
                    />
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="inertia-table-pagination">
            <pagination :links="records.links" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3'; // or @inertiajs/inertia-vue if using Inertia v1
import debounce from 'lodash/debounce';

// Import components
import Pagination from './Pagination.vue';
import TextColumn from './Columns/TextColumn.vue';
import BadgeColumn from './Columns/BadgeColumn.vue';
import ButtonAction from './Actions/ButtonAction.vue';
import SelectFilter from './Filters/SelectFilter.vue';

const props = defineProps({
    table: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const tableData = computed(() => props.table);

const columns = computed(() => tableData.value.columns);
const actions = computed(() => tableData.value.actions);
const filters = computed(() => tableData.value.filters);
const records = computed(() => tableData.value.records);
const sortable = computed(() => tableData.value.sortable);
const searchable = computed(() => tableData.value.searchable);
const preserveState = computed(() => tableData.value.preserveState);

// State
const activeSort = ref(null);
const activeFilters = ref({});
const searchQuery = ref('');

onMounted(() => {
    const url = new URL(window.location.href);

    // Initialize sort
    if (url.searchParams.has('sort')) {
        activeSort.value = url.searchParams.get('sort');
    }

    // Initialize filters
    filters.value.forEach((filter) => {
        const paramName = `filters[${filter.name}]`;
        if (url.searchParams.has(paramName)) {
            activeFilters.value[filter.name] = url.searchParams.get(paramName);
        } else if (filter.default !== null) {
            activeFilters.value[filter.name] = filter.default;
        }
    });

    // Initialize search
    if (url.searchParams.has('search')) {
        searchQuery.value = url.searchParams.get('search');
    }
});

// Component mapping
const getColumnComponent = (type) => {
    const components = {
        text: TextColumn,
        badge: BadgeColumn,
        // Add more column types here
    };

    return components[type] || TextColumn;
};

const getActionComponent = (type) => {
    const components = {
        button: ButtonAction,
        // Add more action types here
    };

    return components[type] || ButtonAction;
};

const getFilterComponent = (type) => {
    const components = {
        select: SelectFilter,
        // Add more filter types here
    };

    return components[type] || SelectFilter;
};

// Actions
const toggleSort = (column) => {
    if (activeSort.value === column) {
        activeSort.value = `-${column}`;
    } else if (activeSort.value === `-${column}`) {
        activeSort.value = null;
    } else {
        activeSort.value = column;
    }

    updateUrl();
};

const getSortIcon = (column) => {
    if (activeSort.value === column) {
        return 'sort-asc';
    } else if (activeSort.value === `-${column}`) {
        return 'sort-desc';
    }

    return 'sort';
};

const updateFilter = (name, value) => {
    activeFilters.value[name] = value;
    updateUrl();
};

const debounceSearch = debounce(() => {
    updateUrl();
}, 500);

const handleAction = (action, record) => {
    if (action.url) {
        router.visit(action.url.replace(':id', record.id));
    }
};

// Update URL with current state
const updateUrl = () => {
    const params = {};

    if (activeSort.value) {
        params.sort = activeSort.value;
    }

    if (Object.keys(activeFilters.value).length > 0) {
        params.filters = activeFilters.value;
    }

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    router.get(window.location.pathname, params, {
        preserveState: preserveState.value,
        preserveScroll: true,
        replace: true,
    });
};
</script>

<style>
.inertia-table {
    width: 100%;
}

.inertia-table-table {
    width: 100%;
    border-collapse: collapse;
}

.inertia-table-table th,
.inertia-table-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.inertia-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.inertia-table-sort {
    background: none;
    border: none;
    cursor: pointer;
}

.inertia-table-filters,
.inertia-table-search {
    margin-bottom: 1rem;
}

.inertia-table-actions {
    display: flex;
    gap: 0.5rem;
}

.inertia-table-pagination {
    margin-top: 1rem;
}
</style>
