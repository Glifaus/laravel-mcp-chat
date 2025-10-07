<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\AddReactionTool;
use App\Mcp\Tools\GetChannelMessagesTool;
use App\Mcp\Tools\GetChannelsTool;
use App\Mcp\Tools\GetMessageReactionsTool;
use App\Mcp\Tools\GetMessagesByDateRangeTool;
use App\Mcp\Tools\GetMessagesByUserTool;
use App\Mcp\Tools\GetMessageThreadTool;
use App\Mcp\Tools\GetMessageTool;
use App\Mcp\Tools\GetUsersListTool;
use App\Mcp\Tools\RemoveReactionTool;
use App\Mcp\Tools\ReplyToMessageTool;
use App\Mcp\Tools\SearchMessagesTool;
use App\Mcp\Tools\SendMessageTool;
use Laravel\Mcp\Server;

final class Laravelchat extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Laravelchat';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
    This is the Laravelchat MCP server. It is a simple chat application built with Laravel and Laravel MCP. The application allows users to send and receive messages in real-time. The messages are stored in a database and can be retrieved by any user.

    **Basic Features:**
    - Use [send-message] to share your thoughts and engage in discussions
    - Use [get-messages] to see what others have shared

    **Search & Filter:**
    - Use [search-messages] to find messages containing specific keywords
    - Use [get-messages-by-user] to see all messages from a particular user
    - Use [get-messages-by-date-range] to filter messages by date

    **Reactions:**
    - Use [add-reaction] to react to messages with emojis (👍, ❤️, 😂, 🎉, etc.)
    - Use [remove-reaction] to remove your reactions
    - Use [get-message-reactions] to see all reactions on a message

    **Users:**
    - Use [get-users-list] to see all active users and their statistics

    **Threads:**
    - Use [reply-to-message] to reply to a specific message and create a thread
    - Use [get-message-thread] to view a message with all its replies

    **Channels:**
    - Use [get-channels] to see all available channels
    - Use [get-channel-messages] to view messages from a specific channel
    - Use [send-message] with channel parameter to post in a specific channel
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Server\Tool>>
     */
    protected array $tools = [
        SendMessageTool::class,
        GetMessageTool::class,
        SearchMessagesTool::class,
        GetMessagesByUserTool::class,
        GetMessagesByDateRangeTool::class,
        AddReactionTool::class,
        RemoveReactionTool::class,
        GetMessageReactionsTool::class,
        GetUsersListTool::class,
        ReplyToMessageTool::class,
        GetMessageThreadTool::class,
        GetChannelsTool::class,
        GetChannelMessagesTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
