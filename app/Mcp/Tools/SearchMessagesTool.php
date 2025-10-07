<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class SearchMessagesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Search for messages in the "Laravelchat" chat application by keyword.

    This tool allows you to find messages that contain specific words or phrases in their content.
    The search is case-insensitive and will match partial words.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = $request->string('query')->value();
        $limit = $request->integer('limit', 50);

        $messages = Message::query()
            ->where('content', 'like', "%{$query}%")
            ->latest()
            ->limit($limit)
            ->get();

        if ($messages->isEmpty()) {
            return Response::text(<<<MARKDOWN
                No messages found matching your search query: "{$query}"

                Try using different keywords or check the spelling.
                MARKDOWN);
        }

        $formattedMessages = $messages->map(fn(Message $message): string => sprintf(
            '- **%s** (%s): %s',
            $message->name,
            $message->created_at->diffForHumans(),
            $message->content,
        ))->join("\n");

        $count = $messages->count();

        return Response::text(<<<MARKDOWN
            Found {$count} message(s) matching "{$query}":

            {$formattedMessages}
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
            'query' => $schema->string()
                ->min(1)
                ->max(100)
                ->description('The search query to find in message content.')
                ->required(),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(50)
                ->description('Maximum number of messages to return. Default is 50.'),
        ];
    }
}
