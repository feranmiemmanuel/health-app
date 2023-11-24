<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientBiodataResource;
use App\Models\Patient;
use App\Models\ReminderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function bioData(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'age' => 'required|string',
                'date_of_birth' => 'required|date',
                'blood_group' => 'required|string',
                'genotype' => 'required|string',
                'gender' => 'required|string',
                'emergency_contact_name' => 'required|string',
                'emergency_contact_phone' => 'required|string',
                'emergency_contact_email' => 'required|string',
                'allergies' => 'required|array',
                'condition' => 'required|array',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $patient = Patient::where('user_id', auth()->id())->first();
        $patient->patient_id = uniqid();
        $patient->age = $request->age;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->blood_group = $request->blood_group;
        $patient->genotype = $request->genotype;
        $patient->gender = $request->gender;
        $patient->emergency_contact_name = $request->emergency_contact_name;
        $patient->emergency_contact_phone = $request->emergency_contact_phone;
        $patient->emergency_contact_email = $request->emergency_contact_email;
        $patient->allergies = $request->allergies;
        $patient->condition = $request->condition;
        $patient->save();

        return response()->json([
            'success' => true,
            'message' => 'Bio data filled successfully'
        ], 200);
    }

    public function fetchBioData()
    {
        $patient = Patient::with([
            'user',
            'user.patientHospitals',
            'user.patientHospitals.doctors'
        ])->where('user_id', auth()->id())->first();
        return response()->json([
            'success' => true,
            'message' => 'Bio data fetched successfully',
            'data' => new PatientBiodataResource($patient)
        ]);
    }

    public function reminderHistory(Request $request)
    {
        $perPage = $request->perPage ?? 10;
        $history = ReminderHistory::with(['reminder', 'reminder.medication'])->where('user_id', auth()->id());
        $data = $history->paginate($perPage);
        $countSkipped = (clone $history)->where('status', 'SKIPPED')->count();
        $countPending = (clone $history)->where('status', 'PENDING')->count();
        $countTaken = (clone $history)->where('status', 'TAKEN')->count();
        $totalCount = (clone $history)->count();
        $stat = [
            'total_reminders' => $totalCount,
            'percentage_of_skipped' => $totalCount > 0 ? ($countSkipped / $totalCount) * 100 : 0,
            'percentage_of_pending' => $totalCount > 0 ? ($countPending / $totalCount) * 100 : 0,
            'percentage_adherence' => $totalCount > 0 ? ($countTaken / $totalCount) * 100 : 0,
            'no_of_skipped' => $history->where('status', 'SKIPPED')->count(),
            'no_of_adherance' => $history->where('status', 'TAKEN')->count(),
            'no_of_pending' => $history->where('status', 'PENDING')->count()
        ];
        return $data;
    }
}
