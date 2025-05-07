<?php

declare(strict_types=1);

use Hristijans\LaravelInertiaTable\Actions\ButtonAction;

it('can create a button action', function (): void {
    $buttonAction = ButtonAction::make('edit');

    expect($buttonAction)->toBeAction(ButtonAction::class);
    expect($buttonAction->toArray()['name'])->toBe('edit');
    expect($buttonAction->toArray()['type'])->toBe('button');
});

it('can set url for button action', function (): void {
    $buttonAction = ButtonAction::make('edit')->url('/users/:id/edit');
    expect($buttonAction->toArray()['url'])->toBe('/users/:id/edit');
});

it('can require confirmation for button action', function (): void {
    $buttonAction = ButtonAction::make('delete')->requiresConfirmation();

    expect($buttonAction->toArray()['requiresConfirmation'])->toBeTrue();
});
