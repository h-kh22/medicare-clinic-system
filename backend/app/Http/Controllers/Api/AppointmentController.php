<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::forUser($request->user())->authorize('viewAny', Appointment::class);
        $user = $request->user();
        $query = Appointment::with(['doctor.user', 'doctor.specialty', 'patient.user']);

        if ($user->isPatient()) {
            $query->where('patient_id', $user->patient?->id);
        } elseif ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor?->id);
        }

        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('appointment_date', $request->date('date')))
            ->when($request->filled('doctor'), fn ($q) => $q->where('doctor_id', $request->integer('doctor')))
            ->when($request->filled('patient'), fn ($q) => $q->where('patient_id', $request->integer('patient')));

        $appointments = $query->orderBy('appointment_date')->orderBy('appointment_time')->paginate(15);

        return $this->successResponse('Appointments retrieved successfully', [
            'appointments' => AppointmentResource::collection($appointments->items()),
            'meta' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
            ],
        ]);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        Gate::forUser(request()->user())->authorize('view', $appointment);
        $appointment->load(['doctor.user', 'doctor.specialty', 'patient.user']);
        return $this->successResponse('Appointment retrieved successfully', new AppointmentResource($appointment));
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $patient = $request->user()->patient;
        abort_if(! $patient, 404, 'Patient profile not found.');
        Gate::forUser($request->user())->authorize('create', Appointment::class);

        $data = $request->validated();
        $appointment = DB::transaction(function () use ($data, $patient) {
            $doctor = Doctor::lockForUpdate()->findOrFail($data['doctor_id']);
            Patient::lockForUpdate()->findOrFail($patient->id);

            $conflictingAppointments = Appointment::whereDate('appointment_date', $data['appointment_date'])
                ->where('status', '!=', 'cancelled')
                ->where(fn ($q) => $q->where('doctor_id', $doctor->id)->orWhere('patient_id', $patient->id))
                ->get(['appointment_time']);
            $conflict = $conflictingAppointments->contains(fn ($existing) => substr((string) $existing->appointment_time, 0, 5) === substr($data['appointment_time'], 0, 5));

            if ($conflict) {
                throw ValidationException::withMessages([
                    'appointment_time' => 'The doctor or patient already has an appointment at this time.',
                ]);
            }

            return Appointment::create([
                ...$data,
                'patient_id' => $patient->id,
                'status' => 'pending',
            ]);
        });

        $appointment->load(['doctor.user', 'doctor.specialty', 'patient.user']);
        return $this->successResponse('Appointment created successfully', new AppointmentResource($appointment), 201);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        Gate::forUser($request->user())->authorize('update', $appointment);
        $data = $request->validated();
        $user = $request->user();

        if ($user->isPatient() && ($data['status'] ?? null) !== 'cancelled') {
            return $this->errorResponse('Patients may only cancel appointments.', null, 403);
        }

        if (isset($data['appointment_date']) || isset($data['appointment_time'])) {
            $date = $data['appointment_date'] ?? $appointment->appointment_date?->format('Y-m-d');
            $time = $data['appointment_time'] ?? substr((string) $appointment->appointment_time, 0, 5);
            $conflictingAppointments = Appointment::where('id', '!=', $appointment->id)
                ->whereDate('appointment_date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(fn ($q) => $q->where('doctor_id', $appointment->doctor_id)->orWhere('patient_id', $appointment->patient_id))
                ->get(['appointment_time']);
            $conflict = $conflictingAppointments->contains(fn ($existing) => substr((string) $existing->appointment_time, 0, 5) === substr($time, 0, 5));
            if ($conflict) return $this->errorResponse('The doctor or patient already has an appointment at this time.', null, 422);
        }
        $appointment->update($data);
        $appointment->load(['doctor.user', 'doctor.specialty', 'patient.user']);
        return $this->successResponse('Appointment updated successfully', new AppointmentResource($appointment));
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        Gate::forUser($request->user())->authorize('delete', $appointment);
        $appointment->update(['status' => 'cancelled']);
        return $this->successResponse('Appointment cancelled successfully', null);
    }
}
