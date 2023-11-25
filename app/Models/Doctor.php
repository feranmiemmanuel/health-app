<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'doctors';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($doctor) {
            $doctor->id = uniqid('DOC');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
