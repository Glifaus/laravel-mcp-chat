<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\CreateMessageAction;
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
    Use this tool to send a message to the "Laravelchat" chat application.
    MARKDOWN;

    /** @var array|string[] */
    private array $ignorableNames = [
        'user',
        'anonymous',
        'assistant',
        'claude',
        'gpt',
        'chatgpt',
        'bard',
        'bing',
        'sydney',
        'Cursor',
    ];

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, CreateMessageAction $action): Response
    {
        $request->validate([
            'name' => 'required|string|min:1|max:50',
            'content' => 'required|string|min:1|max:500',
            'channel' => 'string|min:1|max:50',
        ]);

        $name = $request->string('name')->value();
        $content = $request->string('content')->value();
        $channel = $request->string('channel', 'general')->value();

        if (in_array(mb_strtolower($name), $this->ignorableNames, true)) {
            return Response::error(
                <<<'MARKDOWN'
            Please provide your real first name. Avoid using generic names like "User", "Anonymous", "Assistant", "Claude", "GPT", or similar.
            MARKDOWN
            );
        }

        $message = $action->handle($name, $content, $channel);

        return Response::text(
            "Your message has been successfully sent to **#{$channel}**.\n\n" .
                "Message ID: #{$message->id}\n\n" .
                "💡 You may view messages in this channel using [get-channel-messages]."
        );
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
                ->max(50)
                ->description(
                    <<<'MARKDOWN'
                    The name of the user sending the message.

                    Don't use "User" or "Anonymous", or "Assistant", or "Claude", or "GPT", or any other generic name.

                    IMPORTANT: Ask the user for their first name if you don't know it.
                    MARKDOWN
                ),
            'content' => $schema->string()
                ->min(1)
                ->max(500)
                ->description('The content of the message to send.')
                ->required(),
            'channel' => $schema->string()
                ->min(1)
                ->max(50)
                ->description('The channel to send the message to (e.g., "general", "php", "python"). Defaults to "general".'),
        ];
    }
}
