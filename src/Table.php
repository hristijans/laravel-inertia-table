<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

final class Table
{
    protected string $name;

    protected array $columns = [];

    protected array $actions = [];

    protected array $filters = [];

    protected ?Builder $query = null;

    protected int $perPage = 15;

    protected bool $preserveState = false;

    protected string $pageName = 'page';

    protected array $sortableColumns = [];

    protected array $searchableColumns = [];

    public static function make(string $name): self
    {
        $instance = new self;
        $instance->name = $name;

        return $instance;
    }

    public function columns(array $columns): self
    {
        $this->columns = $columns;

        foreach ($columns as $column) {
            if ($column->isSortable()) {
                $this->sortableColumns[] = $column->getName();
            }

            if ($column->isSearchable()) {
                $this->searchableColumns[] = $column->getName();
            }
        }

        return $this;
    }

    public function actions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function query(Builder $builder): self
    {
        $this->query = $builder;

        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function preserveState(bool $preserve = true): self
    {
        $this->preserveState = $preserve;

        return $this;
    }

    public function getTableData(): array
    {
        $request = app(Request::class);

        $state = $this->getState($request);

        if (! $this->query instanceof \Illuminate\Database\Eloquent\Builder) {
            throw new \RuntimeException('Query must be set before rendering the table.');
        }

        $query = $this->applyFiltersToQuery($this->query, $state['filters'] ?? []);
        $query = $this->applySortingToQuery($query, $state['sort'] ?? null);
        $query = $this->applySearchToQuery($query, $state['search'] ?? null);

        $lengthAwarePaginator = $query->paginate(
            $this->perPage,
            ['*'],
            $this->pageName,
            $state['page'] ?? 1
        )->withQueryString();

        return [
            'name' => $this->name,
            'columns' => collect($this->columns)->map->toArray()->toArray(),
            'actions' => collect($this->actions)->map->toArray()->toArray(),
            'filters' => collect($this->filters)->map->toArray()->toArray(),
            'records' => $lengthAwarePaginator,
            'sortable' => $this->sortableColumns,
            'searchable' => $this->searchableColumns,
            'preserveState' => $this->preserveState,
        ];
    }

    public function render(): array
    {
        $tableData = $this->getTableData();

        // In a real application, you'd use something like:
        // Inertia::share('table', $tableData);
        // But for testing purposes, we'll just return the data

        return $tableData;
    }

    protected function applyFiltersToQuery(Builder $builder, array $filters): Builder
    {
        foreach ($filters as $name => $value) {
            if (empty($value)) {
                continue;
            }

            $filter = collect($this->filters)->first(fn ($filter): bool => $filter->getName() === $name);

            if ($filter) {
                $builder = $filter->apply($builder, $value);
            }
        }

        return $builder;
    }

    protected function applySortingToQuery(Builder $builder, ?string $sort): Builder
    {
        if ($sort === null || $sort === '' || $sort === '0') {
            return $builder;
        }

        $direction = 'asc';
        $column = $sort;

        if (Str::startsWith($sort, '-')) {
            $direction = 'desc';
            $column = Str::substr($sort, 1);
        }

        if (in_array($column, $this->sortableColumns)) {
            return $builder->orderBy($column, $direction);
        }

        return $builder;
    }

    protected function applySearchToQuery(Builder $builder, ?string $search): Builder
    {
        if ($search === null || $search === '' || $search === '0' || $this->searchableColumns === []) {
            return $builder;
        }

        return $builder->where(function ($query) use ($search): void {
            foreach ($this->searchableColumns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $query->{$method}($column, 'LIKE', "%{$search}%");
            }
        });
    }

    protected function getState(Request $request): array
    {
        if (! $this->preserveState) {
            return $request->all();
        }

        try {
            // Try to use the session
            $state = $request->session()->get("tables.{$this->name}", []);

            if ($request->has('sort') || $request->has('filters') || $request->has('search') || $request->has($this->pageName)) {
                $state = array_merge($state, $request->all());
                $request->session()->put("tables.{$this->name}", $state);
            }

            return $state;
        } catch (\RuntimeException) {
            // If session is not available, just return the request parameters
            return $request->all();
        }
    }

    // Helper methods for applying filters, sorting, and search...
}
