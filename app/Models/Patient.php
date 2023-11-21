<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'patients';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($patient) {
            $patient->id = uniqid('Pat');
        });
    }
}
