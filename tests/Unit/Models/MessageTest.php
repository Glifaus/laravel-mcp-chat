<?php

declare(strict_types=1);

use App\Models\Message;

test('to array', function () {
    $message = Message::factory()->create()->refresh();

    expect(array_keys($message->toArray()))
        ->toBe([
            'id',
            'name',
            'content',
            'created_at',
            'updated_at',
        ]);
});
