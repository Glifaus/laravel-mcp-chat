<?php

declare(strict_types=1);

use App\Mcp\Servers\LaravelChat;
use Laravel\Mcp\Facades\Mcp;

// Mcp::web('/mcp/demo', \App\Mcp\Servers\PublicServer::class);
Mcp::web('/mcp/laravel-chat', LaravelChat::class);
