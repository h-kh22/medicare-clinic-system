<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = MedicalRecord::with(['doctor.user', 'patient.user', 'appointment'])->latest();
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isDoctor() || $user->isPatient(), 403);
        if ($user->isPatient()) {
            $query->where('patient_id', $user->patient?->id);
        } elseif ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor?->id);
        }
        return $this->successResponse('Medical records retrieved successfully', MedicalRecordResource::collection($query->get()));
    }

    public function show(Request $request, MedicalRecord $medicalRecord): JsonResponse
    {
        $this->authorizeRecord($request, $medicalRecord);
        $medicalRecord->load(['doctor.user', 'patient.user', 'appointment']);
        return $this->successResponse('Medical record retrieved successfully', new MedicalRecordResource($medicalRecord));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isDoctor() || $request->user()->isAdmin(), 403);
        $data = $request->validate([
            'appointment_id' => ['required', 'integer', 'exists:appointments,id'],
            'diagnosis' => ['required', 'string'],
            'symptoms' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
        $appointment = Appointment::findOrFail($data['appointment_id']);
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->doctor?->id === $appointment->doctor_id, 403);
        abort_unless($appointment->status === 'completed', 422, 'Medical records require a completed appointment.');
        $record = MedicalRecord::create([...$data, 'doctor_id' => $appointment->doctor_id, 'patient_id' => $appointment->patient_id]);
        $record->load(['doctor.user', 'patient.user', 'appointment']);
        return $this->successResponse('Medical record created successfully', new MedicalRecordResource($record), 201);
    }

    public function update(Request $request, MedicalRecord $medicalRecord): JsonResponse
    {
        $this->authorizeRecord($request, $medicalRecord, true);
        $data = $request->validate([
            'diagnosis' => ['sometimes', 'string'], 'symptoms' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'], 'notes' => ['nullable', 'string'],
        ]);
        $medicalRecord->update($data);
        $medicalRecord->load(['doctor.user', 'patient.user', 'appointment']);
        return $this->successResponse('Medical record updated successfully', new MedicalRecordResource($medicalRecord));
    }

    private function authorizeRecord(Request $request, MedicalRecord $record, bool $write = false): void
    {
        $user = $request->user();
        $allowed = $user->isAdmin()
            || ($user->isDoctor() && $user->doctor?->id === $record->doctor_id)
            || (! $write && $user->isPatient() && $user->patient?->id === $record->patient_id);
        abort_unless($allowed, 403);
    }
}
