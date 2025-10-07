<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use App\Models\Reaction;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetMessageReactionsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get all reactions for a specific message in the "Laravelchat" chat application.

    Shows a breakdown of reactions by emoji and who reacted.
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

        $message = Message::query()->find($messageId);
        $reactions = Reaction::query()
            ->where('message_id', $messageId)
            ->get();

        if ($reactions->isEmpty()) {
            return Response::text(
                <<<MARKDOWN
                Message #{$messageId} has no reactions yet.

                Message: "{$message->content}" by {$message->name}

                Be the first to react! Use [add-reaction] to add one.
                MARKDOWN
            );
        }

        // Agrupar reacciones por emoji
        $groupedReactions = $reactions->groupBy('emoji');

        $reactionSummary = $groupedReactions->map(function ($reactions, $emoji) {
            $count = $reactions->count();
            $users = $reactions->pluck('user_name')->join(', ');

            return "- {$emoji} ({$count}): {$users}";
        })->join("\n");

        $totalReactions = $reactions->count();

        return Response::text(
            <<<MARKDOWN
            Reactions for message #{$messageId}:

            Message: "{$message->content}" by {$message->name}

            Total reactions: {$totalReactions}

            {$reactionSummary}
            MARKDOWN
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
            'message_id' => $schema->integer()
                ->description('The ID of the message to get reactions for.')
                ->required(),
        ];
    }
}
