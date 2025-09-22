<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Message;

final readonly class CreateMessageAction
{
    /**
     * Execute the action.
     */
    public function handle(string $name, string $content): Message
    {
        return Message::query()->create([
            'name' => $name,
            'content' => $content,
        ]);
    }
}
