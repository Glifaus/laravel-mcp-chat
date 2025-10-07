<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetMessagesByUserTool;
use App\Models\Message;

it('validates the name argument is required', function () {
    $response = Laravelchat::tool(GetMessagesByUserTool::class);

    $response->assertHasErrors([
        'The name field is required.',
    ]);
});

it('gets messages by user name', function () {
    Message::factory()->create(['name' => 'John Doe', 'content' => 'Hello']);
    Message::factory()->create(['name' => 'Jane Smith', 'content' => 'Hi there']);
    Message::factory()->create(['name' => 'John Doe', 'content' => 'Another message']);

    $response = Laravelchat::tool(GetMessagesByUserTool::class, [
        'name' => 'John Doe',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
    $response->assertSee('Hello');
    $response->assertSee('Another message');
});

it('returns no results when user has no messages', function () {
    Message::factory()->create(['name' => 'John Doe']);

    $response = Laravelchat::tool(GetMessagesByUserTool::class, [
        'name' => 'Jane Smith',
    ]);

    $response->assertOk();
    $response->assertSee('No messages found from user');
});

it('respects the limit parameter when getting messages by user', function () {
    Message::factory()->count(10)->create(['name' => 'John Doe']);

    $response = Laravelchat::tool(GetMessagesByUserTool::class, [
        'name' => 'John Doe',
        'limit' => 5,
    ]);

    $response->assertOk();
    $response->assertSee('Found 5 message(s)');
});

it('performs partial name matching', function () {
    Message::factory()->create(['name' => 'John Doe']);
    Message::factory()->create(['name' => 'Johnny Walker']);

    $response = Laravelchat::tool(GetMessagesByUserTool::class, [
        'name' => 'John',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
});
