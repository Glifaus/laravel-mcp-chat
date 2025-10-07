<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class ReplyToMessageTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Reply to a specific message in the "Laravelchat" chat application, creating a threaded conversation.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'parent_message_id' => 'required|integer|exists:messages,id',
            'name' => 'required|string|min:1|max:50',
            'content' => 'required|string|min:1|max:500',
        ]);

        $parentMessageId = $request->integer('parent_message_id');
        $name = $request->string('name')->value();
        $content = $request->string('content')->value();

        // Get parent message
        $parentMessage = Message::query()->find($parentMessageId);

        if (! $parentMessage) {
            return Response::error("Message #{$parentMessageId} not found.");
        }

        // Create the reply (inherit channel from parent)
        $reply = Message::query()->create([
            'parent_id' => $parentMessageId,
            'name' => $name,
            'content' => $content,
            'channel' => $parentMessage->channel,
        ]);

        $parentPreview = substr($parentMessage->content, 0, 50);
        if (strlen($parentMessage->content) > 50) {
            $parentPreview .= '...';
        }

        return Response::text(
            "Reply successfully posted in **#{$parentMessage->channel}**!\n\n".
            "**Replying to:** {$parentMessage->name}: \"{$parentPreview}\"\n".
            "**Your reply:** {$reply->content}\n\n".
            "Reply ID: #{$reply->id}"
        );
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'parent_message_id' => $schema->integer()
                ->description('The ID of the message to reply to.')
                ->required(),
            'name' => $schema->string()
                ->min(1)
                ->max(50)
                ->description('The name of the user sending the reply.')
                ->required(),
            'content' => $schema->string()
                ->min(1)
                ->max(500)
                ->description('The content of the reply message.')
                ->required(),
        ];
    }
}
