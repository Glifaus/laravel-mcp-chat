<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\CreateMessageAction;
use Closure;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class SendMessageTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Usa esta herramienta para enviar un mensaje al "Knowmadmood Laravel MCP Chat" server.
        Proporciona el contenido del mensaje que deseas enviar.
    MARKDOWN;

    /**
     * @var list<string>
     */
    private array $genericNames = [
        'user',
        'anonymous',
        'assistant',
        'claude',
        'gpt',
        'admin',
        'test',
    ];

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, CreateMessageAction $action): Response
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:1',
                'max:100',
                function (string $attribute, string $value, Closure $fail): void {
                    if (in_array(mb_strtolower($value), $this->genericNames, true)) {
                        $fail('Please provide your real first name. Avoid using generic names like "User", "Anonymous", "Assistant", "Claude", "GPT", or similar.');
                    }
                },
            ],
            'content' => 'required|string|min:1|max:500',
        ]);

        $name = $request->string('name')->value();
        $content = $request->string('content')->value();

        $action->handle(name: $name, content: $content);

        return Response::text(<<<'MARKDOWN'
            Tu mensaje ha sido enviado con éxito al chat.

            Debes ver el mensaje reflejado en el chat público del "Knowmadmood Laravel MCP Chat" server. Enseña al usuario los mensajes actuales en el chat usando la herramienta [get-messages].
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
            'name' => $schema->string()
                ->min(1)
                ->max(100)
                ->required(true)
                ->description(<<<'MARKDOWN'
                    El nombre del usuario que envía el mensaje.
                    Este nombre se mostrará junto al mensaje en el chat.
                    No usar nombres ofensivos o inapropiados.
                    No usar "User", "Anonymous", "Assistant", "Claude", "GPT", o nombres genéricos similares.
                    IMPORTANTE: Usar un nombre único para cada usuario, preguntar el nombre si no lo sabes.
                    MARKDOWN),
            'content' => $schema->string()
                ->min(1)
                ->max(500)
                ->required(true)
                ->description(<<<'MARKDOWN'
                    El contenido del mensaje que deseas enviar al chat.
                    Mantener el mensaje breve y relevante para la conversación.
                    Evitar mensajes ofensivos, inapropiados o fuera de tema.
                    MARKDOWN),
        ];
    }
}
