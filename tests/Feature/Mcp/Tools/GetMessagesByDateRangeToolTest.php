<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetMessagesByDateRangeTool;
use App\Models\Message;
use Carbon\Carbon;

it('gets messages from a specific start date', function () {
    Carbon::setTestNow('2025-10-07 12:00:00');

    Message::factory()->create(['created_at' => '2025-10-05 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-07 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-09 10:00:00']);

    $response = Laravelchat::tool(GetMessagesByDateRangeTool::class, [
        'start_date' => '2025-10-07',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
});

it('gets messages until a specific end date', function () {
    Carbon::setTestNow('2025-10-07 12:00:00');

    Message::factory()->create(['created_at' => '2025-10-05 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-07 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-09 10:00:00']);

    $response = Laravelchat::tool(GetMessagesByDateRangeTool::class, [
        'end_date' => '2025-10-07',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
});

it('gets messages within a date range', function () {
    Carbon::setTestNow('2025-10-07 12:00:00');

    Message::factory()->create(['created_at' => '2025-10-05 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-07 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-08 10:00:00']);
    Message::factory()->create(['created_at' => '2025-10-10 10:00:00']);

    $response = Laravelchat::tool(GetMessagesByDateRangeTool::class, [
        'start_date' => '2025-10-06',
        'end_date' => '2025-10-09',
    ]);

    $response->assertOk();
    $response->assertSee('Found 2 message(s)');
});

it('returns no results when no messages in date range', function () {
    Message::factory()->create(['created_at' => '2025-10-05 10:00:00']);

    $response = Laravelchat::tool(GetMessagesByDateRangeTool::class, [
        'start_date' => '2025-10-10',
        'end_date' => '2025-10-15',
    ]);

    $response->assertOk();
    $response->assertSee('No messages found');
});

it('respects the limit parameter when getting messages by date', function () {
    Message::factory()->count(10)->create(['created_at' => '2025-10-07 10:00:00']);

    $response = Laravelchat::tool(GetMessagesByDateRangeTool::class, [
        'start_date' => '2025-10-07',
        'limit' => 5,
    ]);

    $response->assertOk();
    $response->assertSee('Found 5 message(s)');
});

it('validates end date must be after or equal to start date', function () {
    $response = Laravelchat::tool(GetMessagesByDateRangeTool::class, [
        'start_date' => '2025-10-10',
        'end_date' => '2025-10-05',
    ]);

    $response->assertHasErrors([
        'The end date field must be a date after or equal to start date.',
    ]);
});
