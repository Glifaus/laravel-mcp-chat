<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class GetMessagesByDateRangeTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get messages from a specific date range in the "Laravelchat" chat application.

    This tool allows you to filter messages by date. You can specify a start date, end date, or both.
    Dates should be in ISO 8601 format (e.g., "2025-10-07" or "2025-10-07T10:30:00").
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $startDate = $request->string('start_date')->value();
        $endDate = $request->string('end_date')->value();
        $limit = $request->integer('limit', 50);

        $query = Message::query();

        if ($startDate !== '') {
            $query->where('created_at', '>=', Carbon::parse($startDate));
        }

        if ($endDate !== '') {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $messages = $query
            ->latest()
            ->limit($limit)
            ->get();

        if ($messages->isEmpty()) {
            $dateRange = $this->formatDateRange($startDate, $endDate);

            return Response::text(<<<MARKDOWN
                No messages found{$dateRange}

                Try adjusting your date range or check if there are any messages in the system.
                MARKDOWN);
        }

        $formattedMessages = $messages->map(fn(Message $message): string => sprintf(
            '- **%s** (%s): %s',
            $message->name,
            $message->created_at->format('Y-m-d H:i:s'),
            $message->content,
        ))->join("\n");

        $count = $messages->count();
        $dateRange = $this->formatDateRange($startDate, $endDate);

        return Response::text(<<<MARKDOWN
            Found {$count} message(s){$dateRange}:

            {$formattedMessages}
            MARKDOWN);
    }

    /**
     * Format the date range for display.
     */
    private function formatDateRange(string $startDate, string $endDate): string
    {
        if ($startDate !== '' && $endDate !== '') {
            return " between {$startDate} and {$endDate}";
        }

        if ($startDate !== '') {
            return " from {$startDate} onwards";
        }

        if ($endDate !== '') {
            return " until {$endDate}";
        }

        return '';
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
            'start_date' => $schema->string()
                ->description('The start date for the search range (ISO 8601 format, e.g., "2025-10-07"). Messages from this date onwards will be included.'),
            'end_date' => $schema->string()
                ->description('The end date for the search range (ISO 8601 format, e.g., "2025-10-07"). Messages up to and including this date will be included.'),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(50)
                ->description('Maximum number of messages to return. Default is 50.'),
        ];
    }
}
