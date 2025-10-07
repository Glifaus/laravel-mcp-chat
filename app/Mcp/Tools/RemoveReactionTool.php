<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Reaction;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class RemoveReactionTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Remove a reaction (emoji) from a message in the "Laravelchat" chat application.

    You can only remove your own reactions.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'message_id' => 'required|integer|exists:messages,id',
            'user_name' => 'required|string|min:1|max:50',
            'emoji' => 'required|string|max:10',
        ]);

        $messageId = $request->integer('message_id');
        $userName = $request->string('user_name')->value();
        $emoji = $request->string('emoji')->value();

        $reaction = Reaction::query()
            ->where('message_id', $messageId)
            ->where('user_name', $userName)
            ->where('emoji', $emoji)
            ->first();

        if ($reaction === null) {
            return Response::text(
                <<<MARKDOWN
                You haven't reacted with {$emoji} to this message.

                Use [get-message-reactions] to see current reactions.
                MARKDOWN
            );
        }

        $reaction->delete();

        // Obtener el conteo actualizado de reacciones
        $reactionCounts = Reaction::query()
            ->where('message_id', $messageId)
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->get()
            ->map(fn($reaction) => "{$reaction->emoji} {$reaction->count}")
            ->join(' ');

        $remainingText = $reactionCounts !== '' ? "Remaining reactions: {$reactionCounts}" : 'No reactions remaining on this message.';

        return Response::text(
            <<<MARKDOWN
            Reaction {$emoji} removed successfully from message #{$messageId}!

            {$remainingText}
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
                ->description('The ID of the message to remove the reaction from.')
                ->required(),
            'user_name' => $schema->string()
                ->min(1)
                ->max(50)
                ->description('The name of the user removing the reaction.')
                ->required(),
            'emoji' => $schema->string()
                ->max(10)
                ->description('The emoji reaction to remove.')
                ->required(),
        ];
    }
}
