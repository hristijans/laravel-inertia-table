<template>
  <div class="inertia-table">
    <!-- Filters -->
    <div v-if="filters.length > 0" class="mb-4 p-4 bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="flex flex-wrap gap-4">
        <component
            v-for="filter in filters"
            :key="filter.name"
            :is="getFilterComponent(filter.type)"
            :filter="filter"
            :model-value="activeFilters[filter.name] || filter.default"
            @update:model-value="updateFilter(filter.name, $event)"
        />
      </div>
    </div>

    <!-- Search -->
    <div v-if="searchable.length > 0" class="mb-4">
      <div class="relative rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
            type="text"
            v-model="searchQuery"
            class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Search..."
            @input="debounceSearch"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto shadow-md rounded-lg border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
          <th
              v-for="column in columns"
              :key="column.name"
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            <div class="flex items-center space-x-1">
              <span>{{ column.label }}</span>
              <button
                  v-if="column.sortable"
                  class="focus:outline-none"
                  @click="toggleSort(column.name)"
              >
                  <span v-if="activeSort === column.name" class="text-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                  </span>
                <span v-else-if="activeSort === `-${column.name}`" class="text-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </span>
                <span v-else class="text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                  </span>
              </button>
            </div>
          </th>
          <th
              v-if="actions.length > 0"
              scope="col"
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Actions
          </th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(record, index) in records.data" :key="index" class="hover:bg-gray-50">
          <td
              v-for="column in columns"
              :key="column.name"
              class="px-6 py-4 whitespace-nowrap"
          >
            <component
                :is="getColumnComponent(column.type)"
                :column="column"
                :record="record"
            />
          </td>
          <td v-if="actions.length > 0" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end space-x-2">
              <component
                  v-for="action in actions"
                  :key="action.name"
                  :is="getActionComponent(action.type)"
                  :action="action"
                  :record="record"
                  @action="handleAction(action, record)"
              />
            </div>
          </td>
        </tr>
        <tr v-if="records.data.length === 0">
          <td
              :colspan="actions.length > 0 ? columns.length + 1 : columns.length"
              class="px-6 py-8 text-center text-gray-500"
          >
            No records found
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      <pagination :links="records.links" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3'; // or @inertiajs/inertia-vue if using Inertia v1
import debounce from 'lodash/debounce';
import moment from 'moment';

// Import components
import Pagination from './Pagination.vue';
import TextColumn from './Columns/TextColumn.vue';
import BadgeColumn from './Columns/BadgeColumn.vue';
import DateTimeColumn from './Columns/DateTimeColumn.vue';
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
    datetime: DateTimeColumn,
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