<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospital = new Hospital();
        $hospital->name = 'Hospital1';
        $hospital->email = 'admin@health-app.ng';
        $hospital->save();
    }
}
