<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $guarded = [];
    protected $table = 'hospitals';
    public $incrementing = false;

    public function patients()
    {
        return $this->belongsToMany(User::class, 'user_hospitals', 'hospital_id', 'patient_id');
    }

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'user_hospitals', 'hospital_id', 'doctor_id')
                    ->using(HospitalUser::class);
    }
}

