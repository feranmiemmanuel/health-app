<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'medications';
    public $incrementing = false;

    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($medication) {
    //         $medication->id = uniqid('MED');
    //     });
    // }

    public function reminder()
    {
        return $this->hasOne(Reminder::class, 'medication_id', 'id');
    }
}
