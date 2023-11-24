<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'reminder_histories';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($reminderHistory) {
            $reminderHistory->id = uniqid('HIS');
        });
    }
    public function reminder()
    {
        return $this->belongsTo(Reminder::class, 'reminder_id');
    }
}
