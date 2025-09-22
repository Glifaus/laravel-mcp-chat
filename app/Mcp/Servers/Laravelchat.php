<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetMessageTool;
use App\Mcp\Tools\SendMessageTool;
use Laravel\Mcp\Server;

final class Laravelchat extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Laravelchat';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
    This is the Laravelchat MCP server. It is a simple chat application built with Laravel and Laravel MCP. The application allows users to send and receive messages in real-time. The messages are stored in a database and can be retrieved by any user.
    Users can [send-message] to share their thoughts and engage in discussions.
    Or they can [get-messages] to see what others have shared.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Server\Tool>>
     */
    protected array $tools = [
        SendMessageTool::class,
        GetMessageTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
