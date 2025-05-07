<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Tests\Feature;

use Hristijans\LaravelInertiaTable\Actions\ButtonAction;
use Hristijans\LaravelInertiaTable\Columns\TextColumn;
use Hristijans\LaravelInertiaTable\Filters\SelectFilter;
use Hristijans\LaravelInertiaTable\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        $testUser = new class extends Model
        {
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
        $this->assertEquals('users', $table['name']);
        $this->assertEquals(['name', 'email'], $table['sortable']);
        $this->assertEquals(['name', 'email'], $table['searchable']);
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
        $this->assertEquals('users', $table['name']);
        $this->assertCount(2, $table['actions']);
        $this->assertEquals('edit', $table['actions'][0]['name']);
        $this->assertEquals('/users/:id/edit', $table['actions'][0]['url']);
        $this->assertEquals('delete', $table['actions'][1]['name']);
        $this->assertTrue($table['actions'][1]['requiresConfirmation']);
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
        $this->assertEquals('users', $table['name']);
        $this->assertCount(1, $table['filters']);
        $this->assertEquals('status', $table['filters'][0]['name']);
        $this->assertEquals([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ], $table['filters'][0]['options']);
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
        $this->assertCount(2, $table['records']->items());
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
        $this->assertEquals('Bob Johnson', $table['records']->items()[0]['name']);

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

        $this->assertEquals('John Doe', $table['records']->items()[0]['name']);
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
        $this->assertCount(1, $table['records']->items());
        $this->assertEquals('Jane Smith', $table['records']->items()[0]['name']);
    }

    /** @test */
    /** @test */
    public function it_can_preserve_state()
    {
        // Create a table without state preservation first
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('status'),
            ])
            ->query($this->getTestUserQuery())
            ->render();

        // Check that preserveState is false by default
        $this->assertFalse($table['preserveState']);

        // Now create a table with state preservation
        $table = Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('status'),
            ])
            ->query($this->getTestUserQuery())
            ->preserveState()
            ->render();

        // Check that preserveState is true
        $this->assertTrue($table['preserveState']);
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
        $this->assertEquals(10, $table['records']->perPage());
        $this->assertCount(10, $table['records']->items());

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

        $this->assertEquals(2, $table['records']->currentPage());
        $this->assertCount(10, $table['records']->items());
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
        return new class extends Model
        {
            protected $table = 'test_users';

            protected $guarded = [];
        };
    }
}
