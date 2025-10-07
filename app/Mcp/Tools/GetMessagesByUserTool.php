<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetMessagesByUserTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get all messages from a specific user in the "Laravelchat" chat application.

    This tool allows you to filter messages by the author's name.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|min:1|max:50',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $name = $request->string('name')->value();
        $limit = $request->integer('limit', 50);

        $messages = Message::query()
            ->where('name', 'like', "%{$name}%")
            ->latest()
            ->limit($limit)
            ->get();

        if ($messages->isEmpty()) {
            return Response::text(<<<MARKDOWN
                No messages found from user: "{$name}"

                The user may not have sent any messages yet, or the name might be misspelled.
                MARKDOWN);
        }

        $formattedMessages = $messages->map(fn(Message $message): string => sprintf(
            '- **%s** (%s): %s',
            $message->name,
            $message->created_at->diffForHumans(),
            $message->content,
        ))->join("\n");

        $count = $messages->count();
        $uniqueName = $messages->first()->name;

        return Response::text(<<<MARKDOWN
            Found {$count} message(s) from "{$uniqueName}":

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
            'name' => $schema->string()
                ->min(1)
                ->max(50)
                ->description('The name of the user whose messages you want to retrieve.')
                ->required(),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(50)
                ->description('Maximum number of messages to return. Default is 50.'),
        ];
    }
}
