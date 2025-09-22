<?php

declare(strict_types=1);

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\SendMessageTool;
use App\Models\Message;

it('validate the name argument', function () {
    $response = Laravelchat::tool(SendMessageTool::class);

    $response->assertHasErrors([
        'The content field is required.',
    ]);
});

it('validate the content argument', function () {
    $response = Laravelchat::tool(SendMessageTool::class, [
        'name' => 'John Doe',
    ]);

    $response->assertHasErrors([
        'The content field is required.',
    ]);
});

it('sends a message', function () {
    $response = Laravelchat::tool(SendMessageTool::class, [
        'name' => 'John Doe',
        'content' => 'Hello, world!',
    ]);

    $response->assertOk();
    $response->assertSee('Your message has been successfully sent to the chat.');

    expect(Message::query()->first())->not->toBeNull();

    $this->assertDatabaseHas('messages', [
        'name' => 'John Doe',
        'content' => 'Hello, world!',
    ]);
});

it('ignores generic names', function () {
    $response = Laravelchat::tool(SendMessageTool::class, [
        'name' => 'User',
        'content' => 'Hello, world!',
    ]);

    $response->assertHasErrors([
        'Please provide your real first name. Avoid using generic names like "User", "Anonymous", "Assistant", "Claude", "GPT", or similar.',
    ]);
});
