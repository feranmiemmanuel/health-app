<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'reminders';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($reminder) {
            $reminder->id = uniqid('REM');
        });
    }
}
