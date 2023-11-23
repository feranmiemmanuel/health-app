<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'password_reset_tokens';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'email';
}
