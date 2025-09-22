<?php

declare(strict_types=1);

it('creates a message', function () {
    $action = app(App\Actions\CreateMessageAction::class);

    $message = $action->handle('John Doe', 'Hello, world!');

    expect($message)->toBeInstanceOf(App\Models\Message::class)
        ->and($message->name)->toBe('John Doe')
        ->and($message->content)->toBe('Hello, world!');

    // Verify the message is in the database
    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'name' => 'John Doe',
        'content' => 'Hello, world!',
    ]);
});
