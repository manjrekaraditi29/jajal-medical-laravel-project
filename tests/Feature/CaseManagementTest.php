<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_cases(): void
    {
        $this->get('/cases')->assertRedirect('/login');
    }

    public function test_admin_can_create_a_medical_case(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/cases', [
            'case_number' => 'JM-2026-9901',
            'patient_reference' => 'PT-REF-00123',
            'surgeon_name' => 'Dr. Robert Chen, MD',
            'implant_type' => 'Total Knee Arthroplasty',
            'status' => 'Planning',
            'priority' => 'High',
        ])->assertRedirect();

        $this->assertDatabaseHas('cases', ['case_number' => 'JM-2026-9901']);
    }

    public function test_duplicate_case_number_fails(): void
    {
        $admin = User::factory()->admin()->create();
        MedicalCase::factory()->create(['case_number' => 'JM-DUP', 'created_by' => $admin->id]);

        $this->actingAs($admin)->post('/cases', [
            'case_number' => 'JM-DUP',
            'patient_reference' => 'PT-REF-X',
            'surgeon_name' => 'Dr. X',
            'implant_type' => 'Cage',
            'status' => 'Draft',
            'priority' => 'Low',
        ])->assertSessionHasErrors(['case_number']);
    }

    public function test_viewer_cannot_delete_case_and_receives_403(): void
    {
        $viewer = User::factory()->viewer()->create();
        $case = MedicalCase::factory()->create();

        $this->actingAs($viewer)->delete("/cases/{$case->id}")
            ->assertForbidden();
    }
}