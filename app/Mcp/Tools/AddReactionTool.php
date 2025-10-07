<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use App\Models\Reaction;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class AddReactionTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Add a reaction (emoji) to a message in the "Laravelchat" chat application.

    You can react to messages with emojis like 👍, ❤️, 😂, 🎉, 🚀, etc.
    Each user can only add one reaction of each type per message.
    MARKDOWN;

    /** @var array<string> */
    private array $allowedEmojis = [
        '👍',
        '❤️',
        '😂',
        '🎉',
        '🚀',
        '👏',
        '🔥',
        '💯',
        '👎',
        '😮',
        '😢',
        '😡',
        '🤔',
        '💡',
        '✅',
        '❌',
    ];

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

        // Validar que el emoji esté en la lista permitida
        if (! in_array($emoji, $this->allowedEmojis, true)) {
            return Response::error(
                <<<MARKDOWN
                Invalid emoji. Please use one of the following:
                👍 ❤️ 😂 🎉 🚀 👏 🔥 💯 👎 😮 😢 😡 🤔 💡 ✅ ❌
                MARKDOWN
            );
        }

        $message = Message::query()->find($messageId);

        // Verificar si ya existe la reacción
        $existingReaction = Reaction::query()
            ->where('message_id', $messageId)
            ->where('user_name', $userName)
            ->where('emoji', $emoji)
            ->first();

        if ($existingReaction !== null) {
            return Response::text(
                <<<MARKDOWN
                You have already reacted with {$emoji} to this message.

                Use [remove-reaction] if you want to remove your reaction first.
                MARKDOWN
            );
        }

        Reaction::query()->create([
            'message_id' => $messageId,
            'user_name' => $userName,
            'emoji' => $emoji,
        ]);

        // Obtener el conteo actualizado de reacciones
        $reactionCounts = Reaction::query()
            ->where('message_id', $messageId)
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->get()
            ->map(fn($reaction) => "{$reaction->emoji} {$reaction->count}")
            ->join(' ');

        return Response::text(
            <<<MARKDOWN
            Reaction {$emoji} added successfully to message #{$messageId}!

            Current reactions: {$reactionCounts}

            Message: "{$message->content}" by {$message->name}
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
                ->description('The ID of the message to react to.')
                ->required(),
            'user_name' => $schema->string()
                ->min(1)
                ->max(50)
                ->description('The name of the user adding the reaction.')
                ->required(),
            'emoji' => $schema->string()
                ->max(10)
                ->description('The emoji to react with. Allowed: 👍 ❤️ 😂 🎉 🚀 👏 🔥 💯 👎 😮 😢 😡 🤔 💡 ✅ ❌')
                ->required(),
        ];
    }
}
