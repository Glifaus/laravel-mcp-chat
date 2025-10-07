<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetUsersListTool;
use App\Models\Message;
use Carbon\Carbon;

it('gets list of users with statistics', function () {
    Carbon::setTestNow('2025-10-07 12:00:00');

    Message::factory()->create(['name' => 'John Doe', 'created_at' => '2025-10-07 10:00:00']);
    Message::factory()->create(['name' => 'John Doe', 'created_at' => '2025-10-07 11:00:00']);
    Message::factory()->create(['name' => 'Jane Smith', 'created_at' => '2025-10-07 11:30:00']);

    $response = Laravelchat::tool(GetUsersListTool::class);

    $response->assertOk();
    $response->assertSee('Users in Laravelchat (2 users)');
    $response->assertSee('John Doe');
    $response->assertSee('2 messages');
    $response->assertSee('Jane Smith');
    $response->assertSee('1 message');
    $response->assertSee('Total messages: 3');
});

it('sorts users by message count by default', function () {
    Message::factory()->create(['name' => 'Alice']);
    Message::factory()->count(3)->create(['name' => 'Bob']);
    Message::factory()->count(2)->create(['name' => 'Charlie']);

    $response = Laravelchat::tool(GetUsersListTool::class);

    $response->assertOk();
    $response->assertSee('Bob');
    $response->assertSee('3 messages');
    $response->assertSee('Charlie');
    $response->assertSee('2 messages');
    $response->assertSee('Alice');
    $response->assertSee('1 message');
});

it('sorts users alphabetically when requested', function () {
    Message::factory()->create(['name' => 'Charlie']);
    Message::factory()->create(['name' => 'Alice']);
    Message::factory()->create(['name' => 'Bob']);

    $response = Laravelchat::tool(GetUsersListTool::class, [
        'sort_by' => 'name',
    ]);

    $response->assertOk();
    $response->assertSee('Alice');
    $response->assertSee('Bob');
    $response->assertSee('Charlie');
});

it('sorts users by last activity when requested', function () {
    Carbon::setTestNow('2025-10-07 12:00:00');

    Message::factory()->create(['name' => 'Alice', 'created_at' => '2025-10-05 10:00:00']);
    Message::factory()->create(['name' => 'Bob', 'created_at' => '2025-10-07 10:00:00']);
    Message::factory()->create(['name' => 'Charlie', 'created_at' => '2025-10-06 10:00:00']);

    $response = Laravelchat::tool(GetUsersListTool::class, [
        'sort_by' => 'last_activity',
    ]);

    $response->assertOk();
    $response->assertSee('Alice');
    $response->assertSee('Bob');
    $response->assertSee('Charlie');
});

it('respects limit parameter', function () {
    Message::factory()->create(['name' => 'User 1']);
    Message::factory()->create(['name' => 'User 2']);
    Message::factory()->create(['name' => 'User 3']);

    $response = Laravelchat::tool(GetUsersListTool::class, [
        'limit' => 2,
    ]);

    $response->assertOk();
    $response->assertSee('Users in Laravelchat (2 users)');
});

it('handles empty user list', function () {
    $response = Laravelchat::tool(GetUsersListTool::class);

    $response->assertOk();
    $response->assertSee('No users found in the chat');
    $response->assertSee('Be the first to send a message');
});

it('shows last activity timestamp', function () {
    Carbon::setTestNow('2025-10-07 12:00:00');

    Message::factory()->create([
        'name' => 'John Doe',
        'created_at' => '2025-10-07 10:00:00',
    ]);

    $response = Laravelchat::tool(GetUsersListTool::class);

    $response->assertOk();
    $response->assertSee('last active');
    $response->assertSee('2 hours ago');
});
