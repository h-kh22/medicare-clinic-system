<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDoctorController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $doctors = Doctor::with(['user', 'specialty'])
            ->when($request->filled('search'), fn ($q) => $q->whereHas('user', fn ($user) => $user->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('specialty'), fn ($q) => $q->where('specialty_id', $request->integer('specialty')))
            ->latest()->paginate(15);
        return $this->successResponse('Doctors retrieved successfully', [
            'doctors' => $doctors->through(fn ($doctor) => $this->formatDoctor($doctor)),
            'meta' => ['current_page' => $doctors->currentPage(), 'last_page' => $doctors->lastPage(), 'per_page' => $doctors->perPage(), 'total' => $doctors->total()],
        ]);
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $doctor = DB::transaction(function () use ($request) {
            $user = User::create([...$request->safe()->only(['name', 'email', 'phone']), 'password' => $request->string('password'), 'role' => 'doctor']);
            return Doctor::create([...$request->safe()->only(['specialty_id', 'license_number', 'bio', 'consultation_fee']), 'user_id' => $user->id]);
        });
        return $this->successResponse('Doctor created successfully', $this->formatDoctor($doctor->load(['user', 'specialty'])), 201);
    }

    public function show(Request $request, Doctor $doctor): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        return $this->successResponse('Doctor retrieved successfully', $this->formatDoctor($doctor->load(['user', 'specialty'])));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): JsonResponse
    {
        $data = $request->safe()->all();
        DB::transaction(function () use ($data, $doctor) {
            $userData = array_intersect_key($data, array_flip(['name', 'email', 'phone', 'password']));
            if (array_key_exists('password', $userData) && $userData['password'] === null) unset($userData['password']);
            if ($userData) $doctor->user->update($userData);
            $doctor->update(array_intersect_key($data, array_flip(['specialty_id', 'license_number', 'bio', 'consultation_fee'])));
        });
        return $this->successResponse('Doctor updated successfully', $this->formatDoctor($doctor->fresh()->load(['user', 'specialty'])));
    }

    public function destroy(Request $request, Doctor $doctor): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $doctor->delete();
        return $this->successResponse('Doctor deactivated successfully', null);
    }

    private function formatDoctor(Doctor $doctor): array
    {
        return ['id' => $doctor->id, 'name' => $doctor->user?->name, 'email' => $doctor->user?->email, 'phone' => $doctor->user?->phone, 'specialty' => $doctor->specialty?->name, 'specialty_id' => $doctor->specialty_id, 'license_number' => $doctor->license_number, 'bio' => $doctor->bio, 'consultation_fee' => $doctor->consultation_fee];
    }
}
