<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetMessageThreadTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get a message and all its replies (thread view) from the "Laravelchat" chat application.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'message_id' => 'required|integer|exists:messages,id',
        ]);

        $messageId = $request->integer('message_id');

        $message = Message::query()
            ->with('replies.reactions')
            ->withCount('replies')
            ->find($messageId);

        if (! $message) {
            return Response::error("Message #{$messageId} not found.");
        }

        $output = "Thread for message #{$message->id}:\n\n";
        $output .= "**{$message->name}** ({$message->created_at->diffForHumans()}):\n";
        $output .= "{$message->content}\n";
        $output .= "📊 {$message->replies_count} " . str('reply')->plural($message->replies_count) . "\n\n";

        if ($message->replies->isEmpty()) {
            $output .= "_No replies yet. Be the first to reply!_";
        } else {
            $output .= "**Replies:**\n\n";

            foreach ($message->replies as $reply) {
                $output .= "↳ **{$reply->name}** ({$reply->created_at->diffForHumans()}):\n";
                $output .= "  {$reply->content}\n";

                if ($reply->reactions->isNotEmpty()) {
                    $reactionsSummary = $reply->reactions
                        ->groupBy('emoji')
                        ->map(fn ($group) => $group->first()->emoji . ' ' . $group->count())
                        ->join(' ');

                    $output .= "  Reactions: {$reactionsSummary}\n";
                }

                $output .= "\n";
            }
        }

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
            'message_id' => $schema->integer()
                ->description('The ID of the parent message to view with its thread.')
                ->required(),
        ];
    }
}
