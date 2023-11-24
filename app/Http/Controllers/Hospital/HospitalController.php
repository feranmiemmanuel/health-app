<?php

namespace App\Http\Controllers\Hospital;

use App\Models\Patient;
use App\Models\Hospital;
use Illuminate\Http\Request;
use App\Models\ReminderHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PatientsDataResource;
use App\Http\Resources\HospitalPatientsDataResource;
use App\Http\Resources\PatientReminderHistoryResource;

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

    public function getPatientReminderHistory(Request $request)
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
        $perPage = $request->perPage ?? 10;
        $history = ReminderHistory::with(['reminder', 'reminder.medication'])->where('user_id', $patient->user_id);
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
        return response()->json([
            'success' => true,
            'message' => 'Reminder History Fetched Successfully',
            'stat' => $stat,
            'history' => PatientReminderHistoryResource::collection($data)->response()->getData(true)
        ]);
    }
}
