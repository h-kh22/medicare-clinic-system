<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * PatientController
 *
 * Handles patient listing, viewing, and profile updates.
 * Security:
 * - All routes are protected by auth:sanctum.
 * - PatientPolicy enforces who can see / edit which patient.
 * - Patient IDs from the frontend are NEVER trusted directly;
 *   patients always access their own profile via auth relationship.
 */
class PatientController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/patients
     * Admin: paginated list of all patients with search support.
     * Doctor: paginated list of their appointment-related patients.
     * Patient: forbidden (must use /api/patients/me).
     */
    public function index(Request $request): JsonResponse
    {
        Gate::forUser($request->user())->authorize('viewAny', Patient::class);

        $query = Patient::with('user')->whereHas('user');

        // Admin: full search by name, email, phone
        if ($request->user()->isAdmin()) {
            $search = $request->input('search');

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        }

        // Doctor: only patients who have had appointments with this doctor
        if ($request->user()->isDoctor()) {
            $doctorId = $request->user()->doctor?->id;

            $query->whereHas('appointments', function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId);
            });
        }

        $patients = $query->latest()->paginate(15);

        return $this->successResponse('Patients retrieved successfully', [
            'patients' => PatientResource::collection($patients->items()),
            'meta'     => [
                'current_page' => $patients->currentPage(),
                'last_page'    => $patients->lastPage(),
                'per_page'     => $patients->perPage(),
                'total'        => $patients->total(),
            ],
        ]);
    }

    /**
     * GET /api/patients/{patient}
     * Returns a specific patient's profile.
     * PatientPolicy ensures patients can only see their own.
     */
    public function show(Patient $patient): JsonResponse
    {
        Gate::forUser(request()->user())->authorize('view', $patient);

        $patient->load('user');

        return $this->successResponse(
            'Patient profile retrieved successfully',
            new PatientResource($patient)
        );
    }

    /**
     * GET /api/patients/me
     * Convenience endpoint: patient fetches their own profile without knowing their patient ID.
     * This prevents ID guessing attacks.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->isPatient()) {
            return $this->errorResponse('Only patients can access this endpoint.', null, 403);
        }

        $patient = $user->patient;

        if (! $patient) {
            return $this->errorResponse('Patient profile not found.', null, 404);
        }

        $patient->load('user');

        return $this->successResponse(
            'Your profile retrieved successfully',
            new PatientResource($patient)
        );
    }

    /**
     * PUT /api/patients/{patient}
     * Update a patient's profile.
     * PatientPolicy ensures only admin or the patient themselves can update.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        Gate::forUser($request->user())->authorize('update', $patient);

        return DB::transaction(function () use ($request, $patient) {
            // Update user account fields if provided
            $userFields = $request->only(['name', 'email', 'phone']);
            if (! empty($userFields)) {
                $patient->user->update($userFields);
            }

            // Update patient-specific profile fields
            $patientFields = $request->only([
                'date_of_birth',
                'gender',
                'address',
                'emergency_contact',
            ]);
            if (! empty($patientFields)) {
                $patient->update($patientFields);
            }

            $patient->load('user');

            return $this->successResponse(
                'Patient profile updated successfully',
                new PatientResource($patient)
            );
        });
    }
}
