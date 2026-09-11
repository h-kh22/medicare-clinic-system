<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $completedAppointments = Appointment::where('status', 'completed')->get();

        $recordsData = [
            [
                'diagnosis' => 'Mild Sinus Tachycardia and Stage 1 Prehypertension',
                'symptoms' => 'Occasional rapid heartbeat following caffeine consumption, lightheadedness after intense workouts.',
                'treatment' => 'Dietary sodium reduction, hydration increase, limit caffeine intake, follow-up ECG in 6 months.',
                'notes' => 'Resting blood pressure 132/84 mmHg, heart rate 86 bpm. Normal cardiac auscultation.',
            ],
            [
                'diagnosis' => 'Allergic Rhinitis with Reactive Airway Manifestation',
                'symptoms' => 'Bilateral nasal congestion, clear rhinorrhea, sneezing paroxysms, nocturnal dry cough.',
                'treatment' => 'Cetirizine 10mg once daily, fluticasone nasal spray, environmental allergen management.',
                'notes' => 'Tympanic membranes clear, lungs resonant and free of crackles or wheezes.',
            ],
            [
                'diagnosis' => 'Subacute Contact Dermatitis',
                'symptoms' => 'Pruritic erythematous plaques and micro-vesicles on flexor surfaces of both forearms.',
                'treatment' => 'Hydrocortisone butyrate 0.1% ointment twice daily for 14 days, barrier repair ceramide cream.',
                'notes' => 'Suspected occupational exposure to harsh detergent. Patch testing recommended if refractory.',
            ],
            [
                'diagnosis' => 'Supraspinatus Tendonitis (Grade 1 Rotator Cuff Strain)',
                'symptoms' => 'Anterolateral shoulder pain aggravated by overhead motion and abduction beyond 90 degrees.',
                'treatment' => 'Oral NSAID therapy, ice pack application 15 mins TID, isometric rotator cuff strengthening.',
                'notes' => 'Neer and Hawkins impingement signs mildly positive. No evidence of full-thickness tear.',
            ],
        ];

        foreach ($completedAppointments as $index => $appointment) {
            if (isset($recordsData[$index])) {
                $data = $recordsData[$index];
                MedicalRecord::firstOrCreate(
                    [
                        'appointment_id' => $appointment->id,
                    ],
                    [
                        'doctor_id' => $appointment->doctor_id,
                        'patient_id' => $appointment->patient_id,
                        'diagnosis' => $data['diagnosis'],
                        'symptoms' => $data['symptoms'],
                        'treatment' => $data['treatment'],
                        'notes' => $data['notes'],
                    ]
                );
            }
        }
    }
}
