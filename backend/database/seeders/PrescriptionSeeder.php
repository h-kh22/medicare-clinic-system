<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $completedAppointments = Appointment::where('status', 'completed')->get();

        $prescriptionsData = [
            [
                'notes' => 'Patient advised on proper hydration and monitoring of resting pulse rate.',
                'items' => [
                    [
                        'medicine_name' => 'Metoprolol Tartrate',
                        'dosage' => '25mg',
                        'frequency' => 'Once daily in the morning',
                        'duration' => '30 days',
                        'instructions' => 'Take with breakfast. Monitor pulse and report dizziness.',
                    ],
                    [
                        'medicine_name' => 'Coenzyme Q10',
                        'dosage' => '100mg',
                        'frequency' => 'Once daily with dinner',
                        'duration' => '60 days',
                        'instructions' => 'Nutritional supplement to support myocardial function.',
                    ],
                    [
                        'medicine_name' => 'Omega-3 Acid Ethyl Esters',
                        'dosage' => '1000mg',
                        'frequency' => 'Twice daily',
                        'duration' => '60 days',
                        'instructions' => 'Swallow whole capsule with a full glass of water.',
                    ],
                ],
            ],
            [
                'notes' => 'Use nasal spray regularly for full therapeutic effect over 2 weeks.',
                'items' => [
                    [
                        'medicine_name' => 'Cetirizine Hydrochloride',
                        'dosage' => '10mg',
                        'frequency' => 'Once daily at bedtime',
                        'duration' => '14 days',
                        'instructions' => 'May cause mild drowsiness. Avoid operating heavy machinery.',
                    ],
                    [
                        'medicine_name' => 'Fluticasone Propionate Nasal Spray',
                        'dosage' => '50 mcg/actuation',
                        'frequency' => '1 spray in each nostril daily',
                        'duration' => '30 days',
                        'instructions' => 'Shake gently before use. Blow nose gently before spray administration.',
                    ],
                    [
                        'medicine_name' => 'Hypertonic Saline Nasal Mist',
                        'dosage' => '30ml',
                        'frequency' => 'Twice daily as needed',
                        'duration' => '14 days',
                        'instructions' => 'Use 10 minutes prior to steroid spray to clear mucous.',
                    ],
                ],
            ],
            [
                'notes' => 'Discontinue topical steroid if any skin thinning or burning sensation occurs.',
                'items' => [
                    [
                        'medicine_name' => 'Hydrocortisone Butyrate Cream',
                        'dosage' => '0.1%',
                        'frequency' => 'Apply twice daily',
                        'duration' => '14 days',
                        'instructions' => 'Apply a thin layer strictly to erythematous lesions only.',
                    ],
                    [
                        'medicine_name' => 'Ceramide Barrier Restoring Moisturizer',
                        'dosage' => '200g pump',
                        'frequency' => 'Three times daily',
                        'duration' => '30 days',
                        'instructions' => 'Apply generously to both forearms within 3 minutes of bathing.',
                    ],
                    [
                        'medicine_name' => 'Diphenhydramine HCL',
                        'dosage' => '25mg',
                        'frequency' => 'At bedtime as needed',
                        'duration' => '7 days',
                        'instructions' => 'Take only if nocturnal pruritus prevents restful sleep.',
                    ],
                ],
            ],
            [
                'notes' => 'Follow up in 2 weeks if shoulder pain persists beyond conservative physical therapy.',
                'items' => [
                    [
                        'medicine_name' => 'Ibuprofen Tablets',
                        'dosage' => '600mg',
                        'frequency' => 'Three times daily with meals',
                        'duration' => '10 days',
                        'instructions' => 'Always take with food or milk to safeguard gastric lining.',
                    ],
                    [
                        'medicine_name' => 'Cyclobenzaprine HCL',
                        'dosage' => '5mg',
                        'frequency' => 'Once at bedtime',
                        'duration' => '7 days',
                        'instructions' => 'Muscle relaxant. Absolutely no alcohol during treatment course.',
                    ],
                    [
                        'medicine_name' => 'Diclofenac Sodium Topical Gel',
                        'dosage' => '1%',
                        'frequency' => 'Apply 4g four times daily',
                        'duration' => '14 days',
                        'instructions' => 'Gently massage into right anterior shoulder until fully absorbed.',
                    ],
                ],
            ],
        ];

        foreach ($completedAppointments as $index => $appointment) {
            if (isset($prescriptionsData[$index])) {
                $pData = $prescriptionsData[$index];

                $prescription = Prescription::firstOrCreate(
                    [
                        'appointment_id' => $appointment->id,
                    ],
                    [
                        'doctor_id' => $appointment->doctor_id,
                        'patient_id' => $appointment->patient_id,
                        'prescription_date' => $appointment->appointment_date,
                        'notes' => $pData['notes'],
                    ]
                );

                foreach ($pData['items'] as $item) {
                    PrescriptionItem::firstOrCreate(
                        [
                            'prescription_id' => $prescription->id,
                            'medicine_name' => $item['medicine_name'],
                        ],
                        [
                            'dosage' => $item['dosage'],
                            'frequency' => $item['frequency'],
                            'duration' => $item['duration'],
                            'instructions' => $item['instructions'],
                        ]
                    );
                }
            }
        }
    }
}
