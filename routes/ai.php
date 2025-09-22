<?php

declare(strict_types=1);

use App\Mcp\Servers\Laravelchat;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/chat', Laravelchat::class);
