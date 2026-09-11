<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseAndRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database for every test method
        $this->seed();
    }

    /**
     * Test demo accounts exist with valid credentials and roles.
     */
    public function test_demo_accounts_exist_with_correct_roles_and_passwords(): void
    {
        // Admin
        $admin = User::where('email', 'admin@medicare.test')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue(Hash::check('password123', $admin->password));

        // Doctor
        $doctorUser = User::where('email', 'doctor@medicare.test')->first();
        $this->assertNotNull($doctorUser);
        $this->assertTrue($doctorUser->isDoctor());
        $this->assertTrue(Hash::check('password123', $doctorUser->password));
        $this->assertNotNull($doctorUser->doctor);

        // Patient
        $patientUser = User::where('email', 'patient@medicare.test')->first();
        $this->assertNotNull($patientUser);
        $this->assertTrue($patientUser->isPatient());
        $this->assertTrue(Hash::check('password123', $patientUser->password));
        $this->assertNotNull($patientUser->patient);
    }

    /**
     * Test data counts match Part 2 specifications.
     */
    public function test_seed_counts_match_specification(): void
    {
        $this->assertEquals(1, User::where('role', 'admin')->count());
        $this->assertEquals(5, Doctor::count());
        $this->assertEquals(10, Patient::count());
        $this->assertEquals(5, Specialty::count());
        $this->assertGreaterThanOrEqual(5, Appointment::count());
        $this->assertGreaterThanOrEqual(3, MedicalRecord::count());
        $this->assertGreaterThanOrEqual(3, Prescription::count());
    }

    /**
     * Test Doctor relationships.
     */
    public function test_doctor_relationships(): void
    {
        $doctor = Doctor::first();
        $this->assertNotNull($doctor->user);
        $this->assertInstanceOf(User::class, $doctor->user);
        $this->assertNotNull($doctor->specialty);
        $this->assertInstanceOf(Specialty::class, $doctor->specialty);
        $this->assertTrue($doctor->specialty->doctors->contains($doctor));
        $this->assertNotNull($doctor->appointments);
    }

    /**
     * Test Patient relationships.
     */
    public function test_patient_relationships(): void
    {
        $patient = Patient::first();
        $this->assertNotNull($patient->user);
        $this->assertInstanceOf(User::class, $patient->user);
        $this->assertNotNull($patient->appointments);
    }

    /**
     * Test Appointment, MedicalRecord, and Prescription relationships.
     */
    public function test_appointment_medical_record_and_prescription_relationships(): void
    {
        $appointment = Appointment::where('status', 'completed')->first();
        $this->assertNotNull($appointment);
        $this->assertNotNull($appointment->doctor);
        $this->assertNotNull($appointment->patient);

        if ($appointment->medicalRecord) {
            $record = $appointment->medicalRecord;
            $this->assertEquals($appointment->id, $record->appointment_id);
            $this->assertEquals($appointment->doctor_id, $record->doctor_id);
            $this->assertEquals($appointment->patient_id, $record->patient_id);
        }

        if ($appointment->prescription) {
            $prescription = $appointment->prescription;
            $this->assertEquals($appointment->id, $prescription->appointment_id);
            $this->assertGreaterThanOrEqual(2, $prescription->items->count());

            $firstItem = $prescription->items->first();
            $this->assertInstanceOf(PrescriptionItem::class, $firstItem);
            $this->assertEquals($prescription->id, $firstItem->prescription->id);
        }
    }
}
