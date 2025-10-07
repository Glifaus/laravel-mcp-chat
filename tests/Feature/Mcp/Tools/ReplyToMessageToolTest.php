<?php

declare(strict_types=1);

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\ReplyToMessageTool;
use App\Models\Message;

it('can reply to a message', function () {
    $parentMessage = Message::factory()->create([
        'name' => 'John Doe',
        'content' => 'This is the parent message',
    ]);

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'Jane Smith',
        'content' => 'This is a reply',
    ]);

    $response->assertOk();
    $response->assertSee('Reply successfully posted');
    $response->assertSee('John Doe');
    $response->assertSee('This is a reply');

    expect(Message::query()->count())->toBe(2);

    $reply = Message::query()->where('parent_id', $parentMessage->id)->first();
    expect($reply)->not->toBeNull();
    expect($reply->name)->toBe('Jane Smith');
    expect($reply->content)->toBe('This is a reply');
    expect($reply->parent_id)->toBe($parentMessage->id);
});

it('fails when parent message does not exist', function () {
    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => 999,
        'name' => 'Jane Smith',
        'content' => 'This is a reply',
    ]);

    $response->assertHasErrors();

    expect(Message::query()->count())->toBe(0);
});

it('validates required fields', function () {
    $parentMessage = Message::factory()->create();

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
    ]);

    $response->assertHasErrors();
});

it('validates name length', function () {
    $parentMessage = Message::factory()->create();

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => str_repeat('a', 51),
        'content' => 'This is a reply',
    ]);

    $response->assertHasErrors();
});

it('validates content length', function () {
    $parentMessage = Message::factory()->create();

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'John Doe',
        'content' => str_repeat('a', 501),
    ]);

    $response->assertHasErrors();
});

it('truncates long parent message in response', function () {
    $parentMessage = Message::factory()->create([
        'name' => 'John Doe',
        'content' => str_repeat('This is a very long message. ', 10),
    ]);

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'Jane Smith',
        'content' => 'Short reply',
    ]);

    $response->assertOk();
    $response->assertSee('...');
});

it('shows full parent message if short', function () {
    $parentMessage = Message::factory()->create([
        'name' => 'John Doe',
        'content' => 'Short',
    ]);

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'Jane Smith',
        'content' => 'Reply',
    ]);

    $response->assertOk();
    $response->assertSee('Short');
});

it('can create multiple replies to same parent', function () {
    $parentMessage = Message::factory()->create();

    Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'User 1',
        'content' => 'First reply',
    ]);

    Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'User 2',
        'content' => 'Second reply',
    ]);

    expect(Message::query()->where('parent_id', $parentMessage->id)->count())->toBe(2);
});

it('shows reply ID in response', function () {
    $parentMessage = Message::factory()->create();

    $response = Laravelchat::tool(ReplyToMessageTool::class, [
        'parent_message_id' => $parentMessage->id,
        'name' => 'Jane Smith',
        'content' => 'Reply',
    ]);

    $response->assertOk();
    $response->assertSee('Reply ID:');
});

