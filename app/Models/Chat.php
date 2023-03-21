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
        'studentId',
        'teacherId',
        'campusManagerId',
    ];
    public static function formatMessage($userName, $message, $studentId, $teacherId, $campusManagerId) {
        return [
            'userName' => $userName,
            'message' => $message,
            'studentId' => $studentId,
            'teacherId' => $teacherId,
            'campusManagerId' => $campusManagerId,
            'time' => now()->toDateTimeString(),
        ];
    }
}
