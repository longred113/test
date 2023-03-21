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
        'campusId',
    ];
    public static function formatMessage($userName, $message, $studentId, $teacherId, $campusId) {
        return [
            'userName' => $userName,
            'message' => $message,
            'studentId' => $studentId,
            'teacherId' => $teacherId,
            'campusId' => $campusId,
            'time' => now()->toDateTimeString(),
        ];
    }
}
