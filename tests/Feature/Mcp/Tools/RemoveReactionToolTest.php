<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\RemoveReactionTool;
use App\Models\Message;
use App\Models\Reaction;

it('removes a reaction from a message', function () {
    $message = Message::factory()->create();
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(RemoveReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee('Reaction 👍 removed successfully');
    expect(Reaction::query()->count())->toBe(0);
});

it('handles removing non-existent reaction', function () {
    $message = Message::factory()->create();

    $response = Laravelchat::tool(RemoveReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee("You haven't reacted");
});

it('only removes reaction from correct user', function () {
    $message = Message::factory()->create();
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(RemoveReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'Jane Smith',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee("You haven't reacted");
    expect(Reaction::query()->count())->toBe(1);
});

it('shows remaining reactions after removal', function () {
    $message = Message::factory()->create();
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'Jane Smith',
        'emoji' => '❤️',
    ]);

    $response = Laravelchat::tool(RemoveReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee('Remaining reactions:');
    $response->assertSee('❤️');
});

it('shows message when no reactions remain', function () {
    $message = Message::factory()->create();
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(RemoveReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee('No reactions remaining');
});
