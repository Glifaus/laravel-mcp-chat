<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetMessagesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Usa esta herramienta para obtener una lista de mensajes del servidor "Knowmadmood Laravel MCP Chat".
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
        ]);

        $limit = $request->integer('limit');

        $messages = Message::query()
            ->latest()
            ->limit($limit)
            ->get();

        $messages->map(fn (Message $message): string => sprintf(
            '- **%s**: %s',
            $message->name,
            $message->content,
        ))->join("\n");

        return Response::text(<<<'MARKDOWN'
            Aquí están los últimos mensajes recuperados del servidor "Knowmadmood Laravel MCP Chat":
            $formattedMessages
            MARKDOWN);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     *
     * @codeCoverageIgnore
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()
                ->description('El número máximo de mensajes a recuperar.')
                ->min(1)
                ->max(100)
                ->default(10),
        ];
    }
}
