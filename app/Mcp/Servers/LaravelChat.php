<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetMessagesTool;
use App\Mcp\Tools\SendMessageTool;
use Laravel\Mcp\Server;

final class LaravelChat extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Laravel Chat';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Este es el chat de "Knowmadmood Laravel MCP". Es una aplicación de chat simple en la que los usuarios pueden enviar y ver mensajes.
        Es un chat amigable que que permite a los usuarios interactuar entre sí.
        Proporciona una interfaz sencilla para enviar y ver mensajes.
        Los mensajes se almacenan en una base de datos y se muestran en orden cronológico.
        Los usuarios pueden [send-message] para enviar un nuevo mensaje.
        O pueden [get-messages] para ver los mensajes existentes.

    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Server\Tool>>
     */
    protected array $tools = [
        SendMessageTool::class,
        GetMessagesTool::class,
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
