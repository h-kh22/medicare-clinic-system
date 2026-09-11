<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescriptionResource;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Prescription::with(['doctor.user', 'patient.user', 'appointment', 'items'])->latest();
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isDoctor() || $user->isPatient(), 403);
        if ($user->isPatient()) $query->where('patient_id', $user->patient?->id);
        elseif ($user->isDoctor()) $query->where('doctor_id', $user->doctor?->id);
        return $this->successResponse('Prescriptions retrieved successfully', PrescriptionResource::collection($query->get()));
    }

    public function show(Request $request, Prescription $prescription): JsonResponse
    {
        $this->authorizePrescription($request, $prescription);
        $prescription->load(['doctor.user', 'patient.user', 'appointment', 'items']);
        return $this->successResponse('Prescription retrieved successfully', new PrescriptionResource($prescription));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isDoctor() || $request->user()->isAdmin(), 403);
        $data = $request->validate([
            'appointment_id' => ['required', 'integer', 'exists:appointments,id'],
            'prescription_date' => ['required', 'date'], 'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string', 'max:255'], 'items.*.dosage' => ['required', 'string', 'max:255'],
            'items.*.frequency' => ['required', 'string', 'max:255'], 'items.*.duration' => ['required', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string'],
        ]);
        $appointment = Appointment::findOrFail($data['appointment_id']);
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->doctor?->id === $appointment->doctor_id, 403);
        abort_unless($appointment->status === 'completed', 422, 'Prescriptions require a completed appointment.');
        $prescription = DB::transaction(function () use ($data, $appointment) {
            $prescription = Prescription::create([
                'doctor_id' => $appointment->doctor_id, 'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id, 'prescription_date' => $data['prescription_date'], 'notes' => $data['notes'] ?? null,
            ]);
            $prescription->items()->createMany($data['items']);
            return $prescription;
        });
        $prescription->load(['doctor.user', 'patient.user', 'appointment', 'items']);
        return $this->successResponse('Prescription created successfully', new PrescriptionResource($prescription), 201);
    }

    public function update(Request $request, Prescription $prescription): JsonResponse
    {
        $this->authorizePrescription($request, $prescription, true);
        $data = $request->validate([
            'prescription_date' => ['sometimes', 'date'], 'notes' => ['nullable', 'string'],
            'items' => ['sometimes', 'array', 'min:1'], 'items.*.medicine_name' => ['required_with:items', 'string'],
            'items.*.dosage' => ['required_with:items', 'string'], 'items.*.frequency' => ['required_with:items', 'string'],
            'items.*.duration' => ['required_with:items', 'string'], 'items.*.instructions' => ['nullable', 'string'],
        ]);
        DB::transaction(function () use ($data, $prescription) {
            $prescription->update(collect($data)->except('items')->all());
            if (array_key_exists('items', $data)) { $prescription->items()->delete(); $prescription->items()->createMany($data['items']); }
        });
        $prescription->load(['doctor.user', 'patient.user', 'appointment', 'items']);
        return $this->successResponse('Prescription updated successfully', new PrescriptionResource($prescription));
    }

    private function authorizePrescription(Request $request, Prescription $prescription, bool $write = false): void
    {
        $user = $request->user();
        $allowed = $user->isAdmin() || ($user->isDoctor() && $user->doctor?->id === $prescription->doctor_id) || (! $write && $user->isPatient() && $user->patient?->id === $prescription->patient_id);
        abort_unless($allowed, 403);
    }
}
