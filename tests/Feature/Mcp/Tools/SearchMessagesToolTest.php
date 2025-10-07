<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\SearchMessagesTool;
use App\Models\Message;

it('validates the query argument is required', function () {
    $response = Laravelchat::tool(SearchMessagesTool::class);

    $response->assertHasErrors([
        'The query field is required.',
    ]);
});

it('searches messages by content', function () {
    Message::factory()->create(['content' => 'Hello world']);
    Message::factory()->create(['content' => 'Laravel is amazing']);
    Message::factory()->create(['content' => 'Hello Laravel community']);

    $response = Laravelchat::tool(SearchMessagesTool::class, [
        'query' => 'Laravel',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
    $response->assertSee('Laravel is amazing');
    $response->assertSee('Hello Laravel community');
});

it('returns no results when search query does not match', function () {
    Message::factory()->create(['content' => 'Hello world']);

    $response = Laravelchat::tool(SearchMessagesTool::class, [
        'query' => 'Laravel',
    ]);

    $response->assertOk();
    $response->assertSee('No messages found matching your search query');
});

it('respects the limit parameter in search', function () {
    Message::factory()->count(10)->create(['content' => 'Laravel message']);

    $response = Laravelchat::tool(SearchMessagesTool::class, [
        'query' => 'Laravel',
        'limit' => 5,
    ]);

    $response->assertOk();
    $response->assertSee('Found 5 message(s)');
});

it('performs case-insensitive search', function () {
    Message::factory()->create(['content' => 'HELLO WORLD']);
    Message::factory()->create(['content' => 'hello world']);

    $response = Laravelchat::tool(SearchMessagesTool::class, [
        'query' => 'Hello',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
});
