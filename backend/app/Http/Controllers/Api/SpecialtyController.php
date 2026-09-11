<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSpecialtyRequest;
use App\Http\Requests\Admin\UpdateSpecialtyRequest;
use App\Models\Specialty;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $specialties = Specialty::withCount('doctors')->orderBy('name')->get();
        return $this->successResponse('Specialties retrieved successfully', $specialties);
    }

    public function store(StoreSpecialtyRequest $request): JsonResponse
    {
        return $this->successResponse('Specialty created successfully', Specialty::create($request->validated()), 201);
    }

    public function update(UpdateSpecialtyRequest $request, Specialty $specialty): JsonResponse
    {
        $specialty->update($request->validated());
        return $this->successResponse('Specialty updated successfully', $specialty->loadCount('doctors'));
    }

    public function destroy(Request $request, Specialty $specialty): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        if ($specialty->doctors()->exists()) {
            return $this->errorResponse('Specialties with doctors cannot be deleted.', null, 422);
        }
        $specialty->delete();
        return $this->successResponse('Specialty deleted successfully', null);
    }
}
