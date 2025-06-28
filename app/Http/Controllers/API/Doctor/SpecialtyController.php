<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Helpers\ApiResponse;

class SpecialtyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function show()
    {      
    $specialties = Specialty::select('id', 'name')->get();
        return ApiResponse::sendResponse(200, 'Specialties retrieved successfully', $specialties);
    }

    }

