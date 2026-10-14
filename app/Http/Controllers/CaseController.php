<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MedicalCase::class, 'case');
    }

    public function index(Request $request): View
    {
        $query = MedicalCase::with('creator')
            ->search($request->input('search'))
            ->filterStatus($request->input('status'))
            ->orderBy($request->input('sort_by', 'created_at'), $request->input('sort_dir', 'desc'));

        $cases = $query->paginate(10)->withQueryString();

        return view('cases.index', compact('cases'));
    }

    public function create(): View
    {
        return view('cases.create', ['case' => new MedicalCase()]);
    }

    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);

        return redirect()->route('cases.show', $case)
            ->with('success', "Medical Case {$case->case_number} registered successfully.");
    }

    public function show(MedicalCase $case): View
    {
        $case->load('creator');
        return view('cases.show', compact('case'));
    }

    public function edit(MedicalCase $case): View
    {
        return view('cases.edit', compact('case'));
    }

    public function update(UpdateCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        $case->update($request->validated());

        return redirect()->route('cases.show', $case)
            ->with('success', "Case {$case->case_number} updated successfully.");
    }

    public function destroy(MedicalCase $case): RedirectResponse
    {
        $num = $case->case_number;
        $case->delete();

        return redirect()->route('cases.index')
            ->with('success', "Medical Case {$num} permanently deleted.");
    }
}