<?php

declare(strict_types=1);

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetMessageThreadTool;
use App\Models\Message;
use App\Models\Reaction;

it('can get a message thread with replies', function () {
    $parentMessage = Message::factory()->create([
        'name' => 'John Doe',
        'content' => 'This is the parent message',
    ]);

    $reply1 = Message::factory()->create([
        'parent_id' => $parentMessage->id,
        'name' => 'Jane Smith',
        'content' => 'First reply',
    ]);

    $reply2 = Message::factory()->create([
        'parent_id' => $parentMessage->id,
        'name' => 'Bob Wilson',
        'content' => 'Second reply',
    ]);

    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => $parentMessage->id,
    ]);

    $response->assertOk();
    $response->assertSee('Thread for message');
    $response->assertSee('John Doe');
    $response->assertSee('This is the parent message');
    $response->assertSee('2 replies');
    $response->assertSee('Jane Smith');
    $response->assertSee('First reply');
    $response->assertSee('Bob Wilson');
    $response->assertSee('Second reply');
});

it('shows message with no replies', function () {
    $message = Message::factory()->create([
        'name' => 'John Doe',
        'content' => 'Message without replies',
    ]);

    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => $message->id,
    ]);

    $response->assertOk();
    $response->assertSee('0 replies');
    $response->assertSee('No replies yet');
});

it('fails when message does not exist', function () {
    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => 999,
    ]);

    $response->assertHasErrors();
});

it('shows reactions on replies', function () {
    $parentMessage = Message::factory()->create();

    $reply = Message::factory()->create([
        'parent_id' => $parentMessage->id,
        'name' => 'Jane Smith',
        'content' => 'Reply with reactions',
    ]);

    Reaction::factory()->create([
        'message_id' => $reply->id,
        'emoji' => '👍',
        'user_name' => 'User 1',
    ]);

    Reaction::factory()->create([
        'message_id' => $reply->id,
        'emoji' => '👍',
        'user_name' => 'User 2',
    ]);

    Reaction::factory()->create([
        'message_id' => $reply->id,
        'emoji' => '❤️',
        'user_name' => 'User 3',
    ]);

    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => $parentMessage->id,
    ]);

    $response->assertOk();
    $response->assertSee('Reactions:');
    $response->assertSee('👍 2');
    $response->assertSee('❤️ 1');
});

it('shows correct reply count for single reply', function () {
    $parentMessage = Message::factory()->create();

    Message::factory()->create([
        'parent_id' => $parentMessage->id,
    ]);

    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => $parentMessage->id,
    ]);

    $response->assertOk();
    $response->assertSee('1 reply');
});

it('validates required message_id', function () {
    $response = Laravelchat::tool(GetMessageThreadTool::class, []);

    $response->assertHasErrors();
});

it('shows relative timestamps', function () {
    $parentMessage = Message::factory()->create();

    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => $parentMessage->id,
    ]);

    $response->assertOk();
    $response->assertSee('ago');
});

it('orders replies chronologically', function () {
    $parentMessage = Message::factory()->create([
        'name' => 'Parent',
        'content' => 'Original message',
    ]);

    Message::factory()->create([
        'parent_id' => $parentMessage->id,
        'name' => 'User 1',
        'content' => 'First reply',
        'created_at' => now()->subMinutes(5),
    ]);

    Message::factory()->create([
        'parent_id' => $parentMessage->id,
        'name' => 'User 2',
        'content' => 'Second reply',
        'created_at' => now()->subMinutes(3),
    ]);

    Message::factory()->create([
        'parent_id' => $parentMessage->id,
        'name' => 'User 3',
        'content' => 'Third reply',
        'created_at' => now()->subMinute(),
    ]);

    $response = Laravelchat::tool(GetMessageThreadTool::class, [
        'message_id' => $parentMessage->id,
    ]);

    $response->assertOk();
    $response->assertSee('First reply');
    $response->assertSee('Second reply');
    $response->assertSee('Third reply');
    $response->assertSee('3 replies');
});
