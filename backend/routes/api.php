<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AdminDoctorController;
use App\Http\Controllers\Api\SpecialtyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MediCare API Routes
|--------------------------------------------------------------------------
|
| Base URL: /api
| Standard Response Format:
| { "status": bool, "message": string, "data": array|object|null }
|
*/

// ==========================================
// Public Routes
// ==========================================
Route::get('/health', [HealthController::class, 'check']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
Route::get('/specialties', [SpecialtyController::class, 'index']);

// ==========================================
// Authenticated Routes (Sanctum Protected)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/doctors', [AdminDoctorController::class, 'index']);
        Route::post('/admin/doctors', [AdminDoctorController::class, 'store']);
        Route::get('/admin/doctors/{doctor}', [AdminDoctorController::class, 'show']);
        Route::put('/admin/doctors/{doctor}', [AdminDoctorController::class, 'update']);
        Route::delete('/admin/doctors/{doctor}', [AdminDoctorController::class, 'destroy']);
        Route::post('/admin/specialties', [SpecialtyController::class, 'store']);
        Route::put('/admin/specialties/{specialty}', [SpecialtyController::class, 'update']);
        Route::delete('/admin/specialties/{specialty}', [SpecialtyController::class, 'destroy']);
    });

    // ==========================================
    // Patient Routes
    // ==========================================
    // Convenience: patient accesses own profile without knowing their patient DB ID
    Route::get('/patients/me', [PatientController::class, 'me']);

    // Admin + Doctor: list patients (with search / filter)
    // Admin + Doctor: view individual patient
    // Admin + Patient (own): update patient profile
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);

    // ==========================================
    // Appointment Routes
    // ==========================================
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);

    // ==========================================
    // Clinical Record Routes
    // ==========================================
    Route::get('/medical-records', [MedicalRecordController::class, 'index']);
    Route::get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show']);
    Route::post('/medical-records', [MedicalRecordController::class, 'store']);
    Route::put('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'update']);

    Route::get('/prescriptions', [PrescriptionController::class, 'index']);
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show']);
    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update']);

    // ==========================================
    // Role-Protected Test Routes
    // ==========================================
    Route::middleware('role:admin')->get('/admin/dashboard-test', function (Request $request) {
        return response()->json([
            'status'  => true,
            'message' => 'Admin authorization verified successfully',
            'data'    => [
                'role'       => 'admin',
                'user'       => $request->user()->only(['id', 'name', 'email', 'role']),
                'permission' => 'full_admin_access',
            ],
        ]);
    });

    Route::middleware('role:doctor')->get('/doctor/dashboard-test', function (Request $request) {
        return response()->json([
            'status'  => true,
            'message' => 'Doctor authorization verified successfully',
            'data'    => [
                'role'       => 'doctor',
                'user'       => $request->user()->only(['id', 'name', 'email', 'role']),
                'permission' => 'doctor_clinical_access',
            ],
        ]);
    });

    Route::middleware('role:patient')->get('/patient/dashboard-test', function (Request $request) {
        return response()->json([
            'status'  => true,
            'message' => 'Patient authorization verified successfully',
            'data'    => [
                'role'       => 'patient',
                'user'       => $request->user()->only(['id', 'name', 'email', 'role']),
                'permission' => 'patient_portal_access',
            ],
        ]);
    });
});
