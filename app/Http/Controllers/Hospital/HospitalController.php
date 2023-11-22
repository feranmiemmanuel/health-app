<?php

namespace App\Http\Controllers\Hospital;

use App\Models\Patient;
use App\Models\Hospital;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PatientsDataResource;
use App\Http\Resources\HospitalPatientsDataResource;

class HospitalController extends Controller
{
    public function fetchHospitalPatients(Request $request)
    {
        $perPage = $request->perPage ?? 10;
        $patients = Patient::with(['user', 'user.patientHospitals.doctors'])
                    ->whereHas('user.patientHospitals.doctors', function ($query) {
                        $query->where('users.id', auth()->id());
                    })->orderBy('created_at', 'DESC')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Patients Fetched Successfully',
            'data' => PatientsDataResource::collection($patients)->response()->getData(true)
        ], 200);
    }

    public function fetchHospitalPatientsBioData(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'patient_id' => 'required|exists:patients,id'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
        $patient = Patient::with(['user', 'user.patientHospitals.doctors'])
                    ->whereHas('user.patientHospitals.doctors', function ($query) {
                        $query->where('users.id', auth()->id());
                    })->where('id', $request->patient_id)->first();
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient Not Found'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Patient Bio data fetched successfully',
            'data' => new HospitalPatientsDataResource($patient)
        ]);
    }
}
