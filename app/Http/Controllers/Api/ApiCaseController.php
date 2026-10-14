<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (Gate::denies('viewAny', MedicalCase::class)) {
            return response()->json(['success' => false, 'data' => null, 'message' => 'Unauthorized'], 403);
        }

        $cases = MedicalCase::with('creator:id,name,email,role')->paginate(10);
        return response()->json(['success' => true, 'data' => $cases, 'message' => 'Cases retrieved successfully.'], 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (Gate::denies('create', MedicalCase::class)) {
            return response()->json(['success' => false, 'data' => null, 'message' => 'Only administrators can create cases.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'case_number' => ['required', 'unique:cases,case_number'],
            'patient_reference' => 'required',
            'surgeon_name' => 'required',
            'implant_type' => 'required',
            'status' => ['required', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', Rule::in(MedicalCase::PRIORITIES)],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => ['errors' => $validator->errors()], 'message' => 'Validation error.'], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = $request->user()->id;

        $case = MedicalCase::create($data);
        return response()->json(['success' => true, 'data' => $case, 'message' => 'Medical case created successfully.'], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $case = MedicalCase::with('creator')->find($id);
        if (!$case) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        return response()->json(['success' => true, 'data' => $case, 'message' => 'Case retrieved.'], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $case = MedicalCase::find($id);
        if (!$case) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        if (Gate::denies('update', $case)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $case->update($request->all());
        return response()->json(['success' => true, 'data' => $case, 'message' => 'Case updated.'], 200);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $case = MedicalCase::find($id);
        if (!$case) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        if (Gate::denies('delete', $case)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $case->delete();
        return response()->json(['success' => true, 'data' => ['deleted_id' => $id], 'message' => 'Case deleted.'], 200);
    }
}