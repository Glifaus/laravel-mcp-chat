<?php

declare(strict_types=1);

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetChannelMessagesTool;
use App\Models\Message;

it('gets messages from a specific channel', function () {
    Message::factory()->create([
        'channel' => 'general',
        'name' => 'Alice',
        'content' => 'Hello general',
    ]);
    Message::factory()->create([
        'channel' => 'php',
        'name' => 'Bob',
        'content' => 'Hello PHP',
    ]);
    Message::factory()->create([
        'channel' => 'general',
        'name' => 'Charlie',
        'content' => 'General again',
    ]);

    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'general',
    ]);

    $response->assertOk();
    $response->assertSee('Messages in **#general**');
    $response->assertSee('Alice');
    $response->assertSee('Hello general');
    $response->assertSee('Charlie');
    $response->assertSee('General again');
    $response->assertSee('2 messages');
});

it('does not show messages from other channels', function () {
    Message::factory()->create(['channel' => 'general', 'content' => 'General message']);
    Message::factory()->create(['channel' => 'php', 'content' => 'PHP message']);

    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'general',
    ]);

    $response->assertOk();
    $response->assertSee('General message');
});

it('excludes reply messages from channel view', function () {
    $parent = Message::factory()->create([
        'channel' => 'general',
        'content' => 'Parent message',
    ]);
    Message::factory()->create([
        'channel' => 'general',
        'parent_id' => $parent->id,
        'content' => 'Reply message',
    ]);

    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'general',
    ]);

    $response->assertOk();
    $response->assertSee('Parent message');
    $response->assertSee('1 message');
});

it('validates channel is required', function () {
    $response = Laravelchat::tool(GetChannelMessagesTool::class, []);

    $response->assertHasErrors();
});

it('handles channel with no messages', function () {
    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'empty',
    ]);

    $response->assertOk();
    $response->assertSee('No messages found in channel **#empty**');
});

it('respects limit parameter', function () {
    Message::factory()->count(10)->create(['channel' => 'test']);

    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'test',
        'limit' => 5,
    ]);

    $response->assertOk();
    $response->assertSee('5 messages');
});

it('defaults to 50 messages limit', function () {
    Message::factory()->count(60)->create(['channel' => 'test']);

    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'test',
    ]);

    $response->assertOk();
    $response->assertSee('50 messages');
});

it('shows relative timestamps', function () {
    Message::factory()->create([
        'channel' => 'test',
        'created_at' => now()->subMinutes(5),
    ]);

    $response = Laravelchat::tool(GetChannelMessagesTool::class, [
        'channel' => 'test',
    ]);

    $response->assertOk();
    $response->assertSee('ago');
});
