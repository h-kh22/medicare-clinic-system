<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_doctors_and_specialties_are_listed(): void
    {
        $this->getJson('/api/doctors')->assertOk()->assertJsonPath('status', true)->assertJsonCount(5, 'data');
        $this->getJson('/api/specialties')->assertOk()->assertJsonPath('status', true)->assertJsonCount(5, 'data');
    }

    public function test_admin_can_create_update_and_deactivate_doctor(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $specialty = Specialty::firstOrFail();
        $response = $this->actingAs($admin)->postJson('/api/admin/doctors', [
            'name' => 'Dr. Test Admin', 'email' => 'test.doctor@medicare.test', 'phone' => '555-1234',
            'password' => 'password123', 'password_confirmation' => 'password123', 'specialty_id' => $specialty->id,
            'license_number' => 'MED-TEST-999', 'bio' => 'Test doctor', 'consultation_fee' => 90,
        ]);
        $response->assertCreated()->assertJsonPath('data.name', 'Dr. Test Admin');
        $doctorId = $response->json('data.id');

        $this->actingAs($admin)->putJson('/api/admin/doctors/'.$doctorId, ['bio' => 'Updated bio'])
            ->assertOk()->assertJsonPath('data.bio', 'Updated bio');
        $this->actingAs($admin)->deleteJson('/api/admin/doctors/'.$doctorId)
            ->assertOk()->assertJsonPath('status', true);
        $this->assertSoftDeleted('doctors', ['id' => $doctorId]);
    }

    public function test_non_admin_cannot_manage_doctors(): void
    {
        $doctor = User::where('role', 'doctor')->firstOrFail();
        $this->actingAs($doctor)->getJson('/api/admin/doctors')->assertForbidden();
        $this->actingAs($doctor)->postJson('/api/admin/doctors', [])->assertForbidden();
    }

    public function test_admin_can_manage_specialties_and_cannot_delete_used_specialty(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $response = $this->actingAs($admin)->postJson('/api/admin/specialties', ['name' => 'Neurology', 'description' => 'Brain and nerve care']);
        $response->assertCreated()->assertJsonPath('data.name', 'Neurology');
        $specialtyId = $response->json('data.id');
        $this->actingAs($admin)->putJson('/api/admin/specialties/'.$specialtyId, ['description' => 'Updated description'])
            ->assertOk()->assertJsonPath('data.description', 'Updated description');
        $this->actingAs($admin)->deleteJson('/api/admin/specialties/'.$specialtyId)->assertOk();
        $this->actingAs($admin)->deleteJson('/api/admin/specialties/'.Specialty::withCount('doctors')->firstWhere('doctors_count', '>', 0)->id)->assertStatus(422);
    }
}
