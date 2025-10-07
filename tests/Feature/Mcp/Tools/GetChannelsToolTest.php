<?php

declare(strict_types=1);

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetChannelsTool;
use App\Models\Message;

it('lists all channels with message counts', function () {
    Message::factory()->create(['channel' => 'general']);
    Message::factory()->create(['channel' => 'general']);
    Message::factory()->create(['channel' => 'php']);
    Message::factory()->create(['channel' => 'python']);

    $response = Laravelchat::tool(GetChannelsTool::class, []);

    $response->assertOk();
    $response->assertSee('Active channels');
    $response->assertSee('#general');
    $response->assertSee('2 messages');
    $response->assertSee('#php');
    $response->assertSee('1 message');
    $response->assertSee('#python');
});

it('orders channels by message count', function () {
    Message::factory()->count(5)->create(['channel' => 'popular']);
    Message::factory()->count(2)->create(['channel' => 'medium']);
    Message::factory()->create(['channel' => 'small']);

    $response = Laravelchat::tool(GetChannelsTool::class, []);

    $response->assertOk();
    $response->assertSee('#popular');
    $response->assertSee('5 messages');
});

it('shows last activity for each channel', function () {
    Message::factory()->create([
        'channel' => 'general',
        'created_at' => now()->subHours(2),
    ]);

    $response = Laravelchat::tool(GetChannelsTool::class, []);

    $response->assertOk();
    $response->assertSee('ago');
});

it('handles empty channels list', function () {
    $response = Laravelchat::tool(GetChannelsTool::class, []);

    $response->assertOk();
    $response->assertSee('No channels found');
});

it('shows channel count', function () {
    Message::factory()->create(['channel' => 'general']);
    Message::factory()->create(['channel' => 'php']);
    Message::factory()->create(['channel' => 'python']);

    $response = Laravelchat::tool(GetChannelsTool::class, []);

    $response->assertOk();
    $response->assertSee('3 channels');
});

it('uses plural correctly for single message', function () {
    Message::factory()->create(['channel' => 'test']);

    $response = Laravelchat::tool(GetChannelsTool::class, []);

    $response->assertOk();
    $response->assertSee('1 message');
});
