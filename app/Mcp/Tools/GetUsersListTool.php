<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetUsersListTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get a list of all unique users who have sent messages in the "Laravelchat" chat application.

    Shows user statistics including message count and last activity.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string|in:name,messages,last_activity',
        ]);

        $limit = $request->integer('limit', 50);
        $sortBy = $request->string('sort_by', 'messages')->value();

        // Obtener usuarios únicos con estadísticas
        $users = Message::query()
            ->select('name')
            ->selectRaw('COUNT(*) as message_count')
            ->selectRaw('MAX(created_at) as last_activity')
            ->groupBy('name')
            ->when($sortBy === 'name', fn($query) => $query->orderBy('name'))
            ->when($sortBy === 'messages', fn($query) => $query->orderByDesc('message_count'))
            ->when($sortBy === 'last_activity', fn($query) => $query->orderByDesc('last_activity'))
            ->limit($limit)
            ->get();

        if ($users->isEmpty()) {
            return Response::text(
                <<<'MARKDOWN'
                No users found in the chat.

                Be the first to send a message! Use [send-message] to get started.
                MARKDOWN
            );
        }

        $userList = $users->map(function ($user) {
            $lastActivity = \Carbon\Carbon::parse($user->last_activity)->diffForHumans();

            return sprintf(
                '- **%s** - %d message%s (last active %s)',
                $user->name,
                $user->message_count,
                $user->message_count === 1 ? '' : 's',
                $lastActivity
            );
        })->join("\n");

        $totalUsers = $users->count();
        $totalMessages = $users->sum('message_count');

        return Response::text(
            <<<MARKDOWN
            Users in Laravelchat ({$totalUsers} user{$this->plural($totalUsers)}):

            {$userList}

            Total messages: {$totalMessages}
            MARKDOWN
        );
    }

    /**
     * Get plural suffix.
     */
    private function plural(int $count): string
    {
        return $count === 1 ? '' : 's';
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
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(50)
                ->description('Maximum number of users to return. Default is 50.'),
            'sort_by' => $schema->string()
                ->default('messages')
                ->description('Sort users by: "name" (alphabetically), "messages" (most active), or "last_activity" (most recent). Default is "messages".'),
        ];
    }
}
