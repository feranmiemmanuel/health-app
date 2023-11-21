<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function fetchHospital(Request $request)
    {
        $hospital = Hospital::with('patients')->first();
        // return response()->json([
        //     'data' => $hospital
        // ]);
        return $hospital;
    }
}
