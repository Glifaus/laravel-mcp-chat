<?php

declare(strict_types=1);

use App\Actions\CreateMessageAction;

it('creates messages', function () {
    $action = app(CreateMessageAction::class);
    $message = $action->handle('Test Name', 'This is a test message.');

    expect($message)->toBeInstanceOf(App\Models\Message::class)
        ->and($message->name)->toBe('Test Name')
        ->and($message->content)->toBe('This is a test message.');
});
