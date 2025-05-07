<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inertia Table Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default configuration for the table builder.
    |
    */

    'pagination' => [
        'default_per_page' => 15,
        'page_name' => 'page',
    ],

    'preserve_state' => false,

    'session_key_prefix' => 'tables.',

    'default_column_types' => [
        'text' => Hristijans\LaravelInertiaTable\Columns\TextColumn::class,
        'badge' => Hristijans\LaravelInertiaTable\Columns\BadgeColumn::class,
    ],

    'default_action_types' => [
        'button' => Hristijans\LaravelInertiaTable\Actions\ButtonAction::class,
    ],

    'default_filter_types' => [
        'select' => Hristijans\LaravelInertiaTable\Filters\SelectFilter::class,
    ],
];
