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

    public function query(Builder $query): self
    {
        $this->query = $query;

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

        if ($this->query === null) {
            throw new \RuntimeException('Query must be set before rendering the table.');
        }

        $query = $this->applyFiltersToQuery($this->query, $state['filters'] ?? []);
        $query = $this->applySortingToQuery($query, $state['sort'] ?? null);
        $query = $this->applySearchToQuery($query, $state['search'] ?? null);

        $records = $query->paginate(
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
            'records' => $records,
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

    protected function applyFiltersToQuery(Builder $query, array $filters): Builder
    {
        foreach ($filters as $name => $value) {
            if (empty($value)) {
                continue;
            }

            $filter = collect($this->filters)->first(function ($filter) use ($name) {
                return $filter->getName() === $name;
            });

            if ($filter) {
                $query = $filter->apply($query, $value);
            }
        }

        return $query;
    }

    protected function applySortingToQuery(Builder $query, ?string $sort): Builder
    {
        if (empty($sort)) {
            return $query;
        }

        $direction = 'asc';
        $column = $sort;

        if (Str::startsWith($sort, '-')) {
            $direction = 'desc';
            $column = Str::substr($sort, 1);
        }

        if (in_array($column, $this->sortableColumns)) {
            return $query->orderBy($column, $direction);
        }

        return $query;
    }

    protected function applySearchToQuery(Builder $query, ?string $search): Builder
    {
        if (empty($search) || empty($this->searchableColumns)) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {
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
        } catch (\RuntimeException $e) {
            // If session is not available, just return the request parameters
            return $request->all();
        }
    }

    // Helper methods for applying filters, sorting, and search...
}
