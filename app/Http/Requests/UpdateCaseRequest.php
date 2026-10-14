<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $case = $this->route('case');
        return $this->user() && $this->user()->can('update', $case);
    }

    public function rules(): array
    {
        $case = $this->route('case');
        $caseId = $case instanceof MedicalCase ? $case->id : $case;

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => ['required', 'string', 'max:100'],
            'surgeon_name' => ['required', 'string', 'max:150'],
            'implant_type' => ['required', 'string', 'max:200'],
            'status' => ['required', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', Rule::in(MedicalCase::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }
}