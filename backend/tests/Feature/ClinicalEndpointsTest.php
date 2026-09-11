<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClinicalEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $email): User { return User::where('email', $email)->firstOrFail(); }

    public function test_patient_can_view_and_update_only_own_profile(): void
    {
        $patient = $this->user('patient@medicare.test');
        $other = Patient::where('user_id', '!=', $patient->id)->firstOrFail();
        $this->actingAs($patient)->getJson('/api/patients/me')->assertOk()->assertJsonPath('data.email', $patient->email);
        $this->actingAs($patient)->putJson('/api/patients/'.$patient->patient->id, ['address' => 'Updated address'])->assertOk();
        $this->actingAs($patient)->getJson('/api/patients/'.$other->id)->assertForbidden();
        $this->actingAs($patient)->putJson('/api/patients/'.$other->id, ['address' => 'No'])->assertForbidden();
    }

    public function test_patient_can_book_and_cancel_without_access_to_other_appointments(): void
    {
        $patient = $this->user('patient@medicare.test');
        $doctor = Doctor::firstOrFail();
        $payload = ['doctor_id' => $doctor->id, 'appointment_date' => Carbon::tomorrow()->toDateString(), 'appointment_time' => '23:30', 'reason' => 'Routine check'];
        $response = $this->actingAs($patient)->postJson('/api/appointments', $payload);
        $response->assertCreated()->assertJsonPath('data.status', 'pending');
        $appointmentId = $response->json('data.id');
        $this->actingAs($patient)->getJson('/api/appointments')->assertOk()->assertJsonPath('status', true);
        $this->actingAs($patient)->deleteJson('/api/appointments/'.$appointmentId)->assertOk();
        $this->assertDatabaseHas('appointments', ['id' => $appointmentId, 'status' => 'cancelled']);
    }

    public function test_appointment_rejects_past_dates_and_conflicting_slots(): void
    {
        $patient = $this->user('patient@medicare.test');
        $doctor = Doctor::firstOrFail();
        $payload = ['doctor_id' => $doctor->id, 'appointment_date' => Carbon::yesterday()->toDateString(), 'appointment_time' => '22:00', 'reason' => 'Past'];
        $this->actingAs($patient)->postJson('/api/appointments', $payload)->assertStatus(422)->assertJsonPath('status', false);
        $date = Carbon::tomorrow()->addDay()->toDateString();
        Appointment::create(['doctor_id' => $doctor->id, 'patient_id' => $patient->patient->id, 'appointment_date' => $date, 'appointment_time' => '22:30', 'status' => 'confirmed', 'reason' => 'Existing']);
        $this->actingAs($patient)->postJson('/api/appointments', ['doctor_id' => $doctor->id, 'appointment_date' => $date, 'appointment_time' => '22:30', 'reason' => 'Conflict'])->assertStatus(422);
    }

    public function test_doctor_can_confirm_and_complete_own_appointment_but_not_other_doctors(): void
    {
        $doctorUser = $this->user('doctor@medicare.test');
        $patient = Patient::firstOrFail();
        $appointment = Appointment::create(['doctor_id' => $doctorUser->doctor->id, 'patient_id' => $patient->id, 'appointment_date' => Carbon::tomorrow(), 'appointment_time' => '21:00', 'status' => 'pending', 'reason' => 'Review']);
        $this->actingAs($doctorUser)->putJson('/api/appointments/'.$appointment->id, ['status' => 'confirmed'])->assertOk()->assertJsonPath('data.status', 'confirmed');
        $this->actingAs($doctorUser)->putJson('/api/appointments/'.$appointment->id, ['status' => 'completed'])->assertOk()->assertJsonPath('data.status', 'completed');
        $otherDoctor = User::where('role', 'doctor')->whereKeyNot($doctorUser->id)->firstOrFail();
        $this->actingAs($otherDoctor)->getJson('/api/appointments/'.$appointment->id)->assertForbidden();
    }

    public function test_completed_appointment_allows_authorized_record_and_prescription_only(): void
    {
        $doctor = $this->user('doctor@medicare.test');
        $patient = $this->user('patient@medicare.test');
        $appointment = Appointment::create(['doctor_id' => $doctor->doctor->id, 'patient_id' => $patient->patient->id, 'appointment_date' => Carbon::yesterday(), 'appointment_time' => '20:00', 'status' => 'completed', 'reason' => 'Follow up']);
        $record = $this->actingAs($doctor)->postJson('/api/medical-records', ['appointment_id' => $appointment->id, 'diagnosis' => 'Stable', 'treatment' => 'Continue care']);
        $record->assertCreated()->assertJsonPath('data.patient.id', $patient->patient->id);
        $this->actingAs($patient)->getJson('/api/medical-records/'.$record->json('data.id'))->assertOk();
        $this->actingAs($patient)->getJson('/api/medical-records/'.MedicalRecord::where('patient_id', '!=', $patient->patient->id)->firstOrFail()->id)->assertForbidden();
        $prescription = $this->actingAs($doctor)->postJson('/api/prescriptions', ['appointment_id' => $appointment->id, 'prescription_date' => Carbon::today()->toDateString(), 'items' => [['medicine_name' => 'Test medicine', 'dosage' => '10 mg', 'frequency' => 'Daily', 'duration' => '5 days'], ['medicine_name' => 'Second medicine', 'dosage' => '5 mg', 'frequency' => 'Twice daily', 'duration' => '3 days']]]);
        $prescription->assertCreated()->assertJsonCount(2, 'data.items');
        $this->actingAs($patient)->getJson('/api/prescriptions/'.$prescription->json('data.id'))->assertOk();
    }

    public function test_doctor_cannot_create_record_or_prescription_for_another_doctor_appointment(): void
    {
        $doctor = $this->user('doctor@medicare.test');
        $otherDoctor = User::where('role', 'doctor')->whereKeyNot($doctor->id)->firstOrFail();
        $patient = $this->user('patient@medicare.test');
        $appointment = Appointment::create(['doctor_id' => $otherDoctor->doctor->id, 'patient_id' => $patient->patient->id, 'appointment_date' => Carbon::yesterday(), 'appointment_time' => '19:00', 'status' => 'completed', 'reason' => 'Private']);
        $this->actingAs($doctor)->postJson('/api/medical-records', ['appointment_id' => $appointment->id, 'diagnosis' => 'No'])->assertForbidden();
        $this->actingAs($doctor)->postJson('/api/prescriptions', ['appointment_id' => $appointment->id, 'prescription_date' => Carbon::today()->toDateString(), 'items' => [['medicine_name' => 'No', 'dosage' => '1', 'frequency' => 'Daily', 'duration' => '1 day']]])->assertForbidden();
    }
}
