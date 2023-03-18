<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public static function formatMessage($userName, $message) {
        return [
            'userName' => $userName,
            'message' => $message,
            'time' => now()->toDateTimeString(),
        ];
    }
}
