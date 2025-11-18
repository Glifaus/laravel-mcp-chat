<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;
    //
}
