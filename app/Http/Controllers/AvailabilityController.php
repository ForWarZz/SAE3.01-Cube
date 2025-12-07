<?php

namespace App\Http\Controllers;

use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    protected AvailabilityService $service;

    public function __construct(AvailabilityService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request, $referenceId)
    {
        $sizeId = $request->query('size');

        $availabilities = $this->service->getAvailabilities($referenceId, $sizeId);

        return response()->json([
            'success' => true,
            'reference_id' => $referenceId,
            'size_filter' => $sizeId,
            'availabilities' => $availabilities,
        ]);
    }
}
