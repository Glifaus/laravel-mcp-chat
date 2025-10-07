<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetChannelsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get a list of all active channels in the "Laravelchat" chat application.
    Shows channel names with message counts and last activity.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $channels = Message::query()
            ->selectRaw('channel, COUNT(*) as message_count, MAX(created_at) as last_activity')
            ->groupBy('channel')
            ->orderBy('message_count', 'desc')
            ->get();

        if ($channels->isEmpty()) {
            return Response::text('No channels found. Send a message to create the first channel!');
        }

        $output = "Active channels in Laravelchat ({$channels->count()} channels):\n\n";

        foreach ($channels as $channel) {
            $lastActivity = $channel->last_activity 
                ? \Carbon\Carbon::parse($channel->last_activity)->diffForHumans()
                : 'never';

            $output .= "- **#{$channel->channel}** - {$channel->message_count} " 
                . str('message')->plural($channel->message_count) 
                . " (last activity {$lastActivity})\n";
        }

        $output .= "\n💡 Use [get-channel-messages] to view messages from a specific channel.";

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
