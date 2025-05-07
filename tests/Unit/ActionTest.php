<?php

declare(strict_types=1);

use Hristijans\LaravelInertiaTable\Actions\ButtonAction;

it('can create a button action', function () {
    $action = ButtonAction::make('edit');

    expect($action)->toBeAction(ButtonAction::class);
    expect($action->toArray()['name'])->toBe('edit');
    expect($action->toArray()['type'])->toBe('button');
});

it('can set url for button action', function () {
    $action = ButtonAction::make('edit')->url('/users/:id/edit');
    expect($action->toArray()['url'])->toBe('/users/:id/edit');
});

it('can require confirmation for button action', function () {
    $action = ButtonAction::make('delete')->requiresConfirmation();

    expect($action->toArray()['requiresConfirmation'])->toBeTrue();
});
