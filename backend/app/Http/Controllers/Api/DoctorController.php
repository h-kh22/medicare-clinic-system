<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $doctors = Doctor::with(['user', 'specialty'])
            ->when($request->filled('search'), fn ($q) => $q->whereHas('user', fn ($user) => $user->where('name', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('specialty'), fn ($q) => $q->where('specialty_id', $request->integer('specialty')))
            ->get()
            ->map(fn ($doctor) => $this->formatDoctor($doctor));
        return $this->successResponse('Doctors retrieved successfully', $doctors);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load(['user', 'specialty']);
        return $this->successResponse('Doctor retrieved successfully', $this->formatDoctor($doctor));
    }

    private function formatDoctor(Doctor $doctor): array
    {
        return ['id' => $doctor->id, 'name' => $doctor->user?->name, 'email' => $doctor->user?->email, 'specialty' => $doctor->specialty?->name, 'bio' => $doctor->bio, 'consultation_fee' => $doctor->consultation_fee];
    }
}
