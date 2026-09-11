<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    use ApiResponse;

    /**
     * Health check endpoint for MediCare API.
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        return $this->successResponse(
            'MediCare API is running',
            [
                'application' => 'MediCare',
                'version' => '1.0.0',
            ]
        );
    }
}
