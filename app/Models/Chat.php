<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'userName',
        'message',
        'userId',
    ];
    public static function formatMessage($userName, $message, $userId) {
        return [
            'userName' => $userName,
            'message' => $message,
            'userId' => $userId,
            'time' => now()->toDateTimeString(),
        ];
    }
}
