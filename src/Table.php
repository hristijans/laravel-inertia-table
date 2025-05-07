<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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

    public function render(): void
    {
        $request = app(Request::class);

        $state = $this->getState($request);

        $query = $this->applyFiltersToQuery($this->query, $state['filters'] ?? []);
        $query = $this->applySortingToQuery($query, $state['sort'] ?? null);
        $query = $this->applySearchToQuery($query, $state['search'] ?? null);

        $records = $query->paginate(
            $this->perPage,
            ['*'],
            $this->pageName,
            $state['page'] ?? 1
        )->withQueryString();

        $tableData = [
            'name' => $this->name,
            'columns' => collect($this->columns)->map->toArray()->toArray(),
            'actions' => collect($this->actions)->map->toArray()->toArray(),
            'filters' => collect($this->filters)->map->toArray()->toArray(),
            'records' => $records,
            'sortable' => $this->sortableColumns,
            'searchable' => $this->searchableColumns,
            'preserveState' => $this->preserveState,
        ];

        Inertia::share('table', $tableData);
    }

    protected function getState(Request $request): array
    {
        if (! $this->preserveState) {
            return $request->all();
        }

        $state = $request->session()->get("tables.{$this->name}", []);

        if ($request->has('sort') || $request->has('filters') || $request->has('search') || $request->has($this->pageName)) {
            $state = array_merge($state, $request->all());
            $request->session()->put("tables.{$this->name}", $state);
        }

        return $state;
    }

    // Helper methods for applying filters, sorting, and search...
}
