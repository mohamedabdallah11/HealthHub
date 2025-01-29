<?php

namespace App\Http\Controllers\API\AdminDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Helpers\ApiResponse;


class AdminSpecialtyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specialties',
        ]);

        $specialty = Specialty::create([
            'name' => $request->name,
        ]);

        return ApiResponse::sendResponse(201, 'Specialty added successfully', $specialty);
    }

    public function update(Request $request, $id)
    {

        $specialty = Specialty::find($id);
        if (!$specialty) {
            return ApiResponse::sendResponse(404, 'Specialty not found', []);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,'. $id,
        ]);
        $specialty->update([
            'name' => $request->name,
        ]);
        return ApiResponse::sendResponse(200, 'Specialty updated successfully', $specialty);
    }

    public function destroy($id)
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return ApiResponse::sendResponse(404, 'Specialty not found', []);
        }

        $specialty->delete();
        return ApiResponse::sendResponse(200, 'Specialty deleted successfully', []);
    }
}
