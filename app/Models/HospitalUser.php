<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HospitalUser extends Pivot
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'user_hospitals';
}
