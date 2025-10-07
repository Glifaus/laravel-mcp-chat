<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetMessageReactionsTool;
use App\Models\Message;
use App\Models\Reaction;

it('gets reactions for a message', function () {
    $message = Message::factory()->create(['content' => 'Hello world']);
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'Jane Smith',
        'emoji' => '👍',
    ]);
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'Bob Wilson',
        'emoji' => '❤️',
    ]);

    $response = Laravelchat::tool(GetMessageReactionsTool::class, [
        'message_id' => $message->id,
    ]);

    $response->assertOk();
    $response->assertSee('Total reactions: 3');
    $response->assertSee('👍 (2)');
    $response->assertSee('John Doe');
    $response->assertSee('Jane Smith');
    $response->assertSee('❤️ (1)');
    $response->assertSee('Bob Wilson');
});

it('handles message with no reactions', function () {
    $message = Message::factory()->create(['content' => 'Hello world']);

    $response = Laravelchat::tool(GetMessageReactionsTool::class, [
        'message_id' => $message->id,
    ]);

    $response->assertOk();
    $response->assertSee('has no reactions yet');
    $response->assertSee('Be the first to react');
});

it('shows message content in response', function () {
    $message = Message::factory()->create([
        'name' => 'John Doe',
        'content' => 'Test message content',
    ]);
    Reaction::factory()->create([
        'message_id' => $message->id,
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(GetMessageReactionsTool::class, [
        'message_id' => $message->id,
    ]);

    $response->assertOk();
    $response->assertSee('Test message content');
    $response->assertSee('John Doe');
});

it('validates message_id exists', function () {
    $response = Laravelchat::tool(GetMessageReactionsTool::class, [
        'message_id' => 999,
    ]);

    $response->assertHasErrors([
        'The selected message id is invalid.',
    ]);
});
