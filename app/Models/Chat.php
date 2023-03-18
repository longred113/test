<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'userName',
        'message',
        'studentId',
        'teacherId'
    ];
    public static function formatMessage($userName, $message, $studentId, $teacherId) {
        return [
            'userName' => $userName,
            'message' => $message,
            'studentId' => $studentId,
            'teacherId' => $teacherId,
            'time' => now()->toDateTimeString(),
        ];
    }
}
