<?php

namespace Hristijans\LaravelInertiaTable\Tests\Feature;

use Hristijans\LaravelInertiaTable\Table;
use Hristijans\LaravelInertiaTable\Columns\TextColumn;
use Hristijans\LaravelInertiaTable\Actions\ButtonAction;
use Hristijans\LaravelInertiaTable\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia;

class TableTest extends \Hristijans\LaravelInertiaTable\Tests\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test table
        Schema::create('test_users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('status');
            $table->timestamps();
        });

        // Create a test model
        $testUser = new class extends Model {
            protected $table = 'test_users';
            protected $guarded = [];
        };

        // Add some test data
        $testUser::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'active',
        ]);

        $testUser::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'status' => 'inactive',
        ]);

        $testUser::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'status' => 'active',
        ]);

        // Mock the Inertia facade
        $this->mock(Inertia::class, function ($mock) {
            $mock->shouldReceive('share')->andReturnSelf();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('test_users');
        parent::tearDown();
    }

    /** @test */
    public function it_can_build_a_basic_table()
    {
        // Create a table
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('status'),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that the table data is correct
        $this->assertTableSharedWithInertia([
            'name' => 'users',
            'sortable' => ['name', 'email'],
            'searchable' => ['name', 'email'],
        ]);
    }

    /** @test */
    public function it_can_add_actions_to_table()
    {
        // Create a table with actions
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable(),
            ])
            ->actions([
                ButtonAction::make('edit')->url('/users/:id/edit'),
                ButtonAction::make('delete')->requiresConfirmation(),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that actions are included
        $this->assertTableSharedWithInertia([
            'name' => 'users',
            'actions' => [
                [
                    'name' => 'edit',
                    'label' => 'Edit',
                    'type' => 'button',
                    'url' => '/users/:id/edit',
                    'requiresConfirmation' => false,
                    'icon' => null,
                    'color' => 'primary',
                    'size' => 'md',
                ],
                [
                    'name' => 'delete',
                    'label' => 'Delete',
                    'type' => 'button',
                    'url' => null,
                    'requiresConfirmation' => true,
                    'icon' => null,
                    'color' => 'primary',
                    'size' => 'md',
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_add_filters_to_table()
    {
        // Create a table with filters
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that filters are included
        $this->assertTableSharedWithInertia([
            'name' => 'users',
            'filters' => [
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'type' => 'select',
                    'default' => null,
                    'options' => [
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ],
                    'multiple' => false,
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_apply_filters_to_query()
    {
        // Set up request with filter
        $request = Request::create('/', 'GET', [
            'filters' => [
                'status' => 'active',
            ],
        ]);

        app()->instance('request', $request);

        // Create a table with filters
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that the filtered records are correct
        $this->assertTableContainsRecordsCount(2); // Only active users
    }

    /** @test */
    public function it_can_apply_sorting_to_query()
    {
        // Set up request with sort
        $request = Request::create('/', 'GET', [
            'sort' => 'name',
        ]);

        app()->instance('request', $request);

        // Create a table with sortable columns
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('email'),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that the sorting is applied
        $this->assertTableFirstRecordName('Bob Johnson');

        // Test reverse sorting
        $request = Request::create('/', 'GET', [
            'sort' => '-name',
        ]);

        app()->instance('request', $request);

        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('email'),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        $this->assertTableFirstRecordName('John Doe');
    }

    /** @test */
    public function it_can_apply_search_to_query()
    {
        // Set up request with search
        $request = Request::create('/', 'GET', [
            'search' => 'jane',
        ]);

        app()->instance('request', $request);

        // Create a table with searchable columns
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that the search is applied
        $this->assertTableContainsRecordsCount(1);
        $this->assertTableFirstRecordName('Jane Smith');
    }

    /** @test */
    public function it_can_preserve_state()
    {
        // Set up request with state
        $request = Request::create('/', 'GET', [
            'sort' => 'name',
            'filters' => [
                'status' => 'active',
            ],
        ]);

        app()->instance('request', $request);

        // Create a table with state preservation
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('status'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->query($this->getTestUserQuery())
            ->preserveState()
            ->render();

        // Check that preserveState is true
        $this->assertTableSharedWithInertia([
            'name' => 'users',
            'preserveState' => true,
        ]);

        // Check session state
        $this->assertSessionHas('tables.users', [
            'sort' => 'name',
            'filters' => [
                'status' => 'active',
            ],
        ]);
    }

    /** @test */
    public function it_can_paginate_results()
    {
        // Add more test data
        $testUser = $this->getTestUserModel();

        for ($i = 0; $i < 20; $i++) {
            $testUser::create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'status' => $i % 2 === 0 ? 'active' : 'inactive',
            ]);
        }

        // Create a table with pagination
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name'),
            ])
            ->query($this->getTestUserQuery())
            ->perPage(10)
            ->render();

        // Check pagination
        $this->assertTablePaginationPerPage(10);
        $this->assertTableContainsRecordsCount(10);

        // Test second page
        $request = Request::create('/', 'GET', [
            'page' => 2,
        ]);

        app()->instance('request', $request);

        $table = Table::make('users')
            ->columns([
                TextColumn::make('name'),
            ])
            ->query($this->getTestUserQuery())
            ->perPage(10)
            ->render();

        $this->assertTablePaginationCurrentPage(2);
        $this->assertTableContainsRecordsCount(10);
    }

    /**
     * Helper method to get test user query.
     */
    private function getTestUserQuery(): Builder
    {
        return $this->getTestUserModel()::query();
    }

    /**
     * Helper method to get test user model.
     */
    private function getTestUserModel()
    {
        return new class extends Model {
            protected $table = 'test_users';
            protected $guarded = [];
        };
    }

    /**
     * Helper method to assert table data shared with Inertia.
     */
    private function assertTableSharedWithInertia(array $expected): void
    {
        $mock = $this->mock(Inertia::class);
        $mock->shouldHaveReceived('share')
            ->with('table', $this->callback(function ($data) use ($expected) {
                foreach ($expected as $key => $value) {
                    if (!isset($data[$key]) || $data[$key] != $value) {
                        return false;
                    }
                }
                return true;
            }));
    }

    /**
     * Helper method to assert table contains specific number of records.
     */
    private function assertTableContainsRecordsCount(int $count): void
    {
        $mock = $this->mock(Inertia::class);
        $mock->shouldHaveReceived('share')
            ->with('table', $this->callback(function ($data) use ($count) {
                return count($data['records']->items()) === $count;
            }));
    }

    /**
     * Helper method to assert table's first record name.
     */
    private function assertTableFirstRecordName(string $name): void
    {
        $mock = $this->mock(Inertia::class);
        $mock->shouldHaveReceived('share')
            ->with('table', $this->callback(function ($data) use ($name) {
                return $data['records']->items()[0]['name'] === $name;
            }));
    }

    /**
     * Helper method to assert table pagination per page.
     */
    private function assertTablePaginationPerPage(int $perPage): void
    {
        $mock = $this->mock(Inertia::class);
        $mock->shouldHaveReceived('share')
            ->with('table', $this->callback(function ($data) use ($perPage) {
                return $data['records']->perPage() === $perPage;
            }));
    }

    /**
     * Helper method to assert table pagination current page.
     */
    private function assertTablePaginationCurrentPage(int $page): void
    {
        $mock = $this->mock(Inertia::class);
        $mock->shouldHaveReceived('share')
            ->with('table', $this->callback(function ($data) use ($page) {
                return $data['records']->currentPage() === $page;
            }));
    }
}
