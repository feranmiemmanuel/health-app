<?php

namespace App\Http\Controllers\Reminders;

use App\Models\User;
use App\Models\Patient;
use App\Models\Reminder;
use App\Models\Medication;
use Illuminate\Http\Request;
use App\Jobs\FetchRemindersJob;
use App\Models\ReminderHistory;
use App\Providers\SendmailEvent;
use App\Jobs\ProcessRemindersJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PatientMedicationResource;
use Illuminate\Support\Facades\Log;

class RemindersController extends Controller
{
    public function createReminderPatient(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'drug_name' => 'required|string',
                'dosage' => 'required|string',
                'dosage_frequency' => 'required|in:ONCE_DAILY,TWICE_DAILY,THRICE_DAILY'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $medId = uniqid('MED');
        $medication = new Medication();
        $medication->id = $medId;
        $medication->name = $request->drug_name;
        $medication->dosage = $request->dosage;
        $medication->user_id = auth()->id();
        $medication->save();

        $reminder = new Reminder();
        $reminder->user_id = auth()->id();
        $reminder->medication_id = $medId;
        $reminder->dosage_frequency = $request->dosage_frequency;
        $reminder->next_reminder_at = $this->calculateNextReminder($request->dosage_frequency);
        $reminder->save();

        $frequency = '';
        switch ($request->dosage_frequency) {
            case 'ONCE_DAILY':
                $frequency = 'Once Everyday';
                break;
            case 'TWICE_DAILY':
                $frequency = 'Twice Everyday';
                break;
            case 'THRICE_DAILY':
                $frequency = 'Three Times Everyday';
                break;
        }
        $details = [
            'title' => 'New Reminder!',
            'subject' => 'A New Reminder Has Been Set For You',
            'content' => [
                'drug_name' => $request->drug_name,
                'dosage' => $request->dosage,
                'frequency' => $frequency,
                'date' => now()
            ],
            'email' => auth()->user()->email,
            'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'sending_type' => 'Verify Email',
            'template' => 'emails/setReminder'
        ];

        event(new SendmailEvent($details));

        return response()->json([
            'success' => true,
            'message' => 'Reminder Set Successfully'
        ], 201);
    }

    public function getMedicationsPatient(Request $request)
    {
        $perPage = $request->per_page ?? 10;
        $medications = Medication::with('reminder')->where('user_id', auth()->id())->orderBy('reminder.next_reminder_at', 'DESC')->paginate($perPage);
        return response()->json([
            'success' => true,
            'message' => 'Medications Fetched Successfully',
            'data' => PatientMedicationResource::collection($medications)->response()->getData(true)
        ]);
    }

    public function createReminderDoctor(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'drug_name' => 'required|string',
                'dosage' => 'required|string',
                'dosage_frequency' => 'required|in:ONCE_DAILY,TWICE_DAILY,THRICE_DAILY',
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

        $medId = uniqid('MED');
        $medication = new Medication();
        $medication->id = $medId;
        $medication->name = $request->drug_name;
        $medication->dosage = $request->dosage;
        $medication->user_id = $patient->user_id;
        $medication->save();

        $reminder = new Reminder();
        $reminder->user_id = $patient->user_id;
        $reminder->medication_id = $medId;
        $reminder->dosage_frequency = $request->dosage_frequency;
        $reminder->next_reminder_at = $this->calculateNextReminder($request->dosage_frequency);
        $reminder->save();

        $user = User::where('id', $patient->user_id)->first();
        $frequency = '';
        switch ($request->dosage_frequency) {
            case 'ONCE_DAILY':
                $frequency = 'Once Everyday';
                break;
            case 'TWICE_DAILY':
                $frequency = 'Twice Everyday';
                break;
            case 'THRICE_DAILY':
                $frequency = 'Three Times Everyday';
                break;
        }
        $details = [
            'title' => 'New Reminder!',
            'subject' => 'A New Reminder Has Been Set For You',
            'content' => [
                'drug_name' => $request->drug_name,
                'dosage' => $request->dosage,
                'frequency' => $frequency,
                'date' => now()
            ],
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'sending_type' => 'Verify Email',
            'template' => 'emails/setReminder'
        ];

        event(new SendmailEvent($details));

        return response()->json([
            'success' => true,
            'message' => 'Reminder Set Successfully'
        ], 201);
    }

    public function getMedicationsDoctor(Request $request)
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

        $perPage = $request->per_page ?? 10;
        $medications = Medication::with('reminder')->where('user_id', $patient->user_id)
                                ->whereHas('reminder', function ($query) {
                                    $query->orderBy('reminders.next_reminder_at', 'DESC');
                                })->paginate($perPage);
        return response()->json([
            'success' => true,
            'message' => 'Medications Fetched Successfully',
            'data' => PatientMedicationResource::collection($medications)->response()->getData(true)
        ]);
    }

    public function fetchDueReminders()
    {
        dispatch(new FetchRemindersJob());
        return true;
    }

    public function processDueReminder($reminder)
    {
        $user = User::where('id', $reminder->user_id)->first();
        $medication = Medication::where('id', $reminder->medication_id)->first();
        //send mail
        $details = [
            'title' => 'Reminder!',
            'subject' => 'Use Your Drugs!',
            'content' => [
                'drug_name' => $medication->drug_name,
                'dosage' => $medication->dosage,
                'date' => now()
            ],
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'sending_type' => 'Verify Email',
            'template' => 'emails/reminder'
        ];

        event(new SendmailEvent($details));
        //send sms

        $nextDueTime = $this->calculateNextReminder($reminder->dosage_frequency);
        
        $history = new ReminderHistory();
        $history->reminder_id = $reminder->id;
        $history->user_id = $user->id;
        $history->reminded_at = $reminder->next_reminder_at;
        $history->save();
        
        $updatedReminder = Reminder::where('id', $reminder->id)->first();
        $updatedReminder->next_reminder_at = $nextDueTime;
        $updatedReminder->status = 'ACTIVE';
        $updatedReminder->save();

        return true;
    }

    public function updatePendingReminder($reminder)
    {
        $reminder1 = Reminder::with('medication')->where('id', $reminder->id)->first();
        $history = ReminderHistory::where('reminder_id', $reminder1->id)->first();
        $history->status = 'SKIPPED';
        $history->save();
        $user = User::where('id', $reminder1->user_id)->first();
        $details = [
            'title' => 'New Reminder Update!',
            'subject' => 'Reminder Update!',
            'content' => [
                'drug_name' => $reminder1->medication->name,
                'dosage' => $reminder1->medication->dosage,
                'status' => 'SKIPPED',
                'date' => now()
            ],
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'sending_type' => 'Verify Email',
            'template' => 'emails/markReminder'
        ];

        event(new SendmailEvent($details));
        return true;
    }

    public function markReminder(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'reminder_id' => 'required|exists:reminders,id',
                'status' => 'required|in:reminders,SKIP,TAKE'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
        $status = '';
        $message = '';
        switch ($request->status) {
            case 'SKIP':
                $status = 'SKIPPED';
                $message = 'Reminder Skipped Successfully';
                break;
            case 'TAKE':
                $status = 'TAKEN';
                $message = 'Reminder Adheared To Successfully';
                break;
        }
        $reminder = Reminder::with('medication')->where('id', $request->reminder_id)->where('user_id', auth()->id())->first();
        $history = ReminderHistory::where('reminder_id', $reminder->id)->first();
        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder Not Found'
            ], 400);
        }
        $history->status = $status;
        $history->save();

        $details = [
            'title' => 'New Reminder!',
            'subject' => 'You ' . $status . ' your drugs',
            'content' => [
                'drug_name' => $reminder->medication->name,
                'dosage' => $reminder->medication->dosage,
                'status' => $status,
                'date' => now()
            ],
            'email' => auth()->user()->email,
            'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'sending_type' => 'Verify Email',
            'template' => 'emails/markReminder'
        ];

        event(new SendmailEvent($details));
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }

    public function calculateNextReminder($dosageFrequency) 
    {
        // $currentTime = time(); // Get current timestamp
        switch ($dosageFrequency) {
            case 'ONCE_DAILY':
                // Calculate next reminder in 24 hours (once a day)
                return strtotime('+24 hours');
                break;
            case 'TWICE_DAILY':
                // Calculate next reminder in 12 hours (twice a day)
                return strtotime('+12 hours');
                break;
            case 'THRICE_DAILY':
                // Calculate next reminder in 8 hours (three times a day)
                return strtotime('+8 hours');
                break;
            // Add more cases for other dosage frequencies if needed
        }
    }

    public function reminderSimulator(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|exists:users,email',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
        $user = User::where('email', $request->email)->first();
        $reminder = Reminder::with('medication')->where('user_id', $user->id)->orderBy('created_at', 'DESC')->first();
        $reminder->next_reminder_at = strtotime(now());
        $reminder->save();

        return response()->json([
            'success' => true,
            'message' => 'Reminder Set To Now Successfully'
        ]);
    }
}
