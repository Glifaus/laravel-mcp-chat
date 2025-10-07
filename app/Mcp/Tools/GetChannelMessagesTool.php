<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetChannelMessagesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get messages from a specific channel in the "Laravelchat" chat application.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'channel' => 'required|string|min:1|max:50',
            'limit' => 'integer|min:1|max:100',
        ]);

        $channel = $request->string('channel')->value();
        $limit = $request->integer('limit', 50);

        $messages = Message::query()
            ->where('channel', $channel)
            ->whereNull('parent_id') // Only main messages, not replies
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse();

        if ($messages->isEmpty()) {
            return Response::text(
                "No messages found in channel **#{$channel}**.\n\n" .
                    "💡 Use [send-message] with channel parameter to post the first message!"
            );
        }

        $output = "Messages in **#{$channel}** ({$messages->count()} messages):\n\n";

        foreach ($messages as $message) {
            $output .= "- **{$message->name}** ({$message->created_at->diffForHumans()}): {$message->content}\n";
        }

        $output .= "\n💡 Use [get-message-thread] to see replies to any message.";

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'channel' => $schema->string()
                ->min(1)
                ->max(50)
                ->description('The name of the channel to get messages from (e.g., "general", "php", "python").')
                ->required(),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->description('Maximum number of messages to return. Default is 50.'),
        ];
    }
}
