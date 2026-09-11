<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        $appointmentsData = [
            [
                'doctor_index' => 0, // Dr. Sarah Jenkins (Cardiology)
                'patient_index' => 0, // John Doe
                'date' => now()->subDays(5)->toDateString(),
                'time' => '09:30:00',
                'status' => 'completed',
                'reason' => 'Routine cardiac evaluation and ECG review.',
                'notes' => 'Patient arrived on time. Baseline vitals recorded within standard ranges.',
            ],
            [
                'doctor_index' => 1, // Dr. Michael Chen (Pediatrics)
                'patient_index' => 1, // Alice Smith
                'date' => now()->subDays(3)->toDateString(),
                'time' => '11:00:00',
                'status' => 'completed',
                'reason' => 'Seasonal respiratory allergies and persistent cough.',
                'notes' => 'Physical examination completed. Mild bronchial irritation detected.',
            ],
            [
                'doctor_index' => 2, // Dr. Emily Rodriguez (Dermatology)
                'patient_index' => 2, // David Wilson
                'date' => now()->subDays(2)->toDateString(),
                'time' => '14:15:00',
                'status' => 'completed',
                'reason' => 'Dermatological screening for persistent contact dermatitis.',
                'notes' => 'Localized erythema on forearms. Topical prescription recommended.',
            ],
            [
                'doctor_index' => 3, // Dr. Robert Kim (Orthopedics)
                'patient_index' => 0, // John Doe
                'date' => now()->subDay()->toDateString(),
                'time' => '10:00:00',
                'status' => 'completed',
                'reason' => 'Acute shoulder pain and rotator cuff strain following tennis match.',
                'notes' => 'Mobility testing showed moderate restriction. Physical therapy advised.',
            ],
            [
                'doctor_index' => 4, // Dr. Lisa Patel (General Medicine)
                'patient_index' => 4, // James Brown
                'date' => now()->addDays(2)->toDateString(),
                'time' => '08:45:00',
                'status' => 'confirmed',
                'reason' => 'Quarterly hypertension and metabolic profile monitoring.',
                'notes' => 'Fasting blood draw scheduled prior to consultation.',
            ],
            [
                'doctor_index' => 0, // Dr. Sarah Jenkins
                'patient_index' => 5, // Olivia Taylor
                'date' => now()->addDays(3)->toDateString(),
                'time' => '15:30:00',
                'status' => 'confirmed',
                'reason' => 'Evaluation for occasional exertion-induced palpitations.',
                'notes' => 'Requested 24-hour Holter monitor fitting.',
            ],
            [
                'doctor_index' => 2, // Dr. Emily Rodriguez
                'patient_index' => 7, // Sophia Martinez
                'date' => now()->addDays(5)->toDateString(),
                'time' => '13:00:00',
                'status' => 'pending',
                'reason' => 'Initial consultation for adult cystic acne treatment protocol.',
                'notes' => 'First-time dermatology visit.',
            ],
            [
                'doctor_index' => 1, // Dr. Michael Chen
                'patient_index' => 6, // William Anderson
                'date' => now()->subDays(1)->toDateString(),
                'time' => '16:00:00',
                'status' => 'cancelled',
                'reason' => 'Routine physical exam.',
                'notes' => 'Cancelled by patient due to work travel. Will re-book next week.',
            ],
        ];

        foreach ($appointmentsData as $data) {
            $doc = $doctors[$data['doctor_index']] ?? $doctors->first();
            $pat = $patients[$data['patient_index']] ?? $patients->first();

            Appointment::firstOrCreate(
                [
                    'doctor_id' => $doc->id,
                    'patient_id' => $pat->id,
                    'appointment_date' => $data['date'],
                    'appointment_time' => $data['time'],
                ],
                [
                    'status' => $data['status'],
                    'reason' => $data['reason'],
                    'notes' => $data['notes'],
                ]
            );
        }
    }
}
