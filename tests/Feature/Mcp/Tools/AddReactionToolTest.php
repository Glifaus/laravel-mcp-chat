<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\AddReactionTool;
use App\Models\Message;
use App\Models\Reaction;

it('validates message_id is required', function () {
    $response = Laravelchat::tool(AddReactionTool::class, [
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertHasErrors([
        'The message id field is required.',
    ]);
});

it('validates message_id exists', function () {
    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => 999,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertHasErrors([
        'The selected message id is invalid.',
    ]);
});

it('validates user_name is required', function () {
    $message = Message::factory()->create();

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'emoji' => '👍',
    ]);

    $response->assertHasErrors([
        'The user name field is required.',
    ]);
});

it('validates emoji is required', function () {
    $message = Message::factory()->create();

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
    ]);

    $response->assertHasErrors([
        'The emoji field is required.',
    ]);
});

it('adds a reaction to a message', function () {
    $message = Message::factory()->create(['content' => 'Hello world']);

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee('Reaction 👍 added successfully');

    expect(Reaction::query()->count())->toBe(1);
    expect(Reaction::query()->first()->emoji)->toBe('👍');
});

it('prevents duplicate reactions from same user', function () {
    $message = Message::factory()->create();
    Reaction::factory()->create([
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    $response->assertSee('You have already reacted');
});

it('allows different users to react with same emoji', function () {
    $message = Message::factory()->create();

    Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'Jane Smith',
        'emoji' => '👍',
    ]);

    $response->assertOk();
    expect(Reaction::query()->count())->toBe(2);
});

it('allows same user to react with different emojis', function () {
    $message = Message::factory()->create();

    Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '❤️',
    ]);

    $response->assertOk();
    expect(Reaction::query()->count())->toBe(2);
});

it('validates emoji is in allowed list', function () {
    $message = Message::factory()->create();

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'John Doe',
        'emoji' => '🦄',
    ]);

    $response->assertHasErrors(['Invalid emoji']);
});

it('shows current reaction counts', function () {
    $message = Message::factory()->create();

    Reaction::factory()->create([
        'message_id' => $message->id,
        'emoji' => '👍',
    ]);

    $response = Laravelchat::tool(AddReactionTool::class, [
        'message_id' => $message->id,
        'user_name' => 'Jane Smith',
        'emoji' => '❤️',
    ]);

    $response->assertOk();
    $response->assertSee('Current reactions:');
    $response->assertSee('👍');
    $response->assertSee('❤️');
});
