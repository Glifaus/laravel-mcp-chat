<?php

declare(strict_types=1);

use App\Mcp\Servers\LaravelChat;
use App\Mcp\Tools\SendMessageTool;
use App\Models\Message;

it('validate the name argument', function () {
    $response = LaravelChat::tool(SendMessageTool::class);

    $response->assertHasErrors([
        'The content field is required.',
    ]);
});

it('validate the content argument', function () {
    $response = LaravelChat::tool(SendMessageTool::class, [
        'name' => 'John Doe',
    ]);

    $response->assertHasErrors([
        'The content field is required.',
    ]);
});

it('sends a message', function () {
    $response = LaravelChat::tool(SendMessageTool::class, [
        'name' => 'Jane',
        'content' => 'Hello, world!',
    ]);

    $response->assertOk();
    $response->assertSee('Tu mensaje ha sido enviado con éxito al chat.');

    expect(Message::count())->toBe(1);
});

it('ignores generic names', function () {
    $response = LaravelChat::tool(SendMessageTool::class, [
        'name' => 'User',
        'content' => 'Hello, world!',
    ]);

    $response->assertHasErrors([
        'Please provide your real first name. Avoid using generic names like "User", "Anonymous", "Assistant", "Claude", "GPT", or similar.',
    ]);
});
