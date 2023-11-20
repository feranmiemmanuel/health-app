<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->first_name = 'Health';
        $user->last_name = 'Admin';
        $user->email = 'admin@health-app.ng';
        $user->phone = '+23480health';
        $user->user_type = 'Admin';
        $user->password = Hash::make('Health1234');
        $user->save();
    }
}
