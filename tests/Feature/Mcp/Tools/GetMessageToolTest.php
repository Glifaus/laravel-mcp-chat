<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp\Tools;

use App\Mcp\Servers\Laravelchat;
use App\Mcp\Tools\GetMessageTool;
use App\Models\Message;

it('retrieves messages', function () {
    Message::factory()->count(10)->create();

    $response = Laravelchat::tool(GetMessageTool::class, [
        'limit' => 5,
    ]);

    $response->assertOk();
});

it('retrieves all messages', function () {
    Message::factory()->count(10)->create();

    $response = Laravelchat::tool(GetMessageTool::class, [
        'limit' => 100,
    ]);

    $response->assertOk();
});

it('limits the maximum number of messages', function () {
    Message::factory()->count(10)->create();

    $response = Laravelchat::tool(GetMessageTool::class, [
        'limit' => 150,
    ]);

    $response->assertHasErrors([
        'The limit field must not be greater than 100.',
    ]);
});
