<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Create Admin Account
        User::firstOrCreate(
            ['email' => 'admin@medicare.test'],
            [
                'name' => 'System Administrator',
                'password' => $defaultPassword,
                'role' => 'admin',
                'phone' => '+1-555-0100',
            ]
        );

        // Fetch specialties for doctor assignments
        $cardiology = Specialty::where('name', 'Cardiology')->first();
        $pediatrics = Specialty::where('name', 'Pediatrics')->first();
        $dermatology = Specialty::where('name', 'Dermatology')->first();
        $orthopedics = Specialty::where('name', 'Orthopedics')->first();
        $generalMedicine = Specialty::where('name', 'General Medicine')->first();

        // 2. Create 5 Doctors
        $doctorsData = [
            [
                'name' => 'Dr. Sarah Jenkins',
                'email' => 'doctor@medicare.test', // Primary demo doctor account
                'phone' => '+1-555-0101',
                'specialty_id' => $cardiology?->id,
                'license_number' => 'MED-CARD-001',
                'bio' => 'Board-certified cardiologist with over 12 years of experience specializing in cardiovascular health and preventative cardiology.',
                'consultation_fee' => 150.00,
            ],
            [
                'name' => 'Dr. Michael Chen',
                'email' => 'michael.chen@medicare.test',
                'phone' => '+1-555-0102',
                'specialty_id' => $pediatrics?->id,
                'license_number' => 'MED-PED-002',
                'bio' => 'Dedicated pediatrician committed to child wellness, immunization regimens, and developmental health monitoring.',
                'consultation_fee' => 120.00,
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'email' => 'emily.rodriguez@medicare.test',
                'phone' => '+1-555-0103',
                'specialty_id' => $dermatology?->id,
                'license_number' => 'MED-DERM-003',
                'bio' => 'Consultant dermatologist focused on clinical dermatology, acne treatments, and advanced skin barrier health.',
                'consultation_fee' => 135.00,
            ],
            [
                'name' => 'Dr. Robert Kim',
                'email' => 'robert.kim@medicare.test',
                'phone' => '+1-555-0104',
                'specialty_id' => $orthopedics?->id,
                'license_number' => 'MED-ORTH-004',
                'bio' => 'Orthopedic surgeon and sports medicine specialist treating joint issues, fractures, and musculoskeletal rehabilitation.',
                'consultation_fee' => 160.00,
            ],
            [
                'name' => 'Dr. Lisa Patel',
                'email' => 'lisa.patel@medicare.test',
                'phone' => '+1-555-0105',
                'specialty_id' => $generalMedicine?->id,
                'license_number' => 'MED-GEN-005',
                'bio' => 'Primary care physician providing holistic family healthcare, chronic condition management, and diagnostic reviews.',
                'consultation_fee' => 100.00,
            ],
        ];

        foreach ($doctorsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $defaultPassword,
                    'role' => 'doctor',
                    'phone' => $data['phone'],
                ]
            );

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id' => $data['specialty_id'],
                    'license_number' => $data['license_number'],
                    'bio' => $data['bio'],
                    'consultation_fee' => $data['consultation_fee'],
                ]
            );
        }

        // 3. Create 10 Patients
        $patientsData = [
            [
                'name' => 'John Doe',
                'email' => 'patient@medicare.test', // Primary demo patient account
                'phone' => '+1-555-0201',
                'date_of_birth' => '1988-04-12',
                'gender' => 'male',
                'address' => '124 Maple Street, Springfield',
                'emergency_contact' => 'Jane Doe (+1-555-0202)',
            ],
            [
                'name' => 'Alice Smith',
                'email' => 'alice.smith@medicare.test',
                'phone' => '+1-555-0203',
                'date_of_birth' => '1992-08-25',
                'gender' => 'female',
                'address' => '456 Oak Avenue, Metropolis',
                'emergency_contact' => 'Bob Smith (+1-555-0204)',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@medicare.test',
                'phone' => '+1-555-0205',
                'date_of_birth' => '1975-11-03',
                'gender' => 'male',
                'address' => '789 Pine Road, Gotham',
                'emergency_contact' => 'Mary Wilson (+1-555-0206)',
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@medicare.test',
                'phone' => '+1-555-0207',
                'date_of_birth' => '1995-02-18',
                'gender' => 'female',
                'address' => '321 Elm Street, Star City',
                'emergency_contact' => 'Carlos Garcia (+1-555-0208)',
            ],
            [
                'name' => 'James Brown',
                'email' => 'james.brown@medicare.test',
                'phone' => '+1-555-0209',
                'date_of_birth' => '1980-07-30',
                'gender' => 'male',
                'address' => '654 Cedar Boulevard, Central City',
                'emergency_contact' => 'Sarah Brown (+1-555-0210)',
            ],
            [
                'name' => 'Olivia Taylor',
                'email' => 'olivia.taylor@medicare.test',
                'phone' => '+1-555-0211',
                'date_of_birth' => '2001-12-14',
                'gender' => 'female',
                'address' => '987 Birch Lane, Keystone',
                'emergency_contact' => 'Mark Taylor (+1-555-0212)',
            ],
            [
                'name' => 'William Anderson',
                'email' => 'william.anderson@medicare.test',
                'phone' => '+1-555-0213',
                'date_of_birth' => '1968-05-22',
                'gender' => 'male',
                'address' => '147 Willow Way, Coast City',
                'emergency_contact' => 'Patricia Anderson (+1-555-0214)',
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophia.martinez@medicare.test',
                'phone' => '+1-555-0215',
                'date_of_birth' => '1990-09-09',
                'gender' => 'female',
                'address' => '258 Walnut Drive, Bludhaven',
                'emergency_contact' => 'Luis Martinez (+1-555-0216)',
            ],
            [
                'name' => 'Alexander Thomas',
                'email' => 'alexander.thomas@medicare.test',
                'phone' => '+1-555-0217',
                'date_of_birth' => '1984-03-17',
                'gender' => 'male',
                'address' => '369 Chestnut Court, Smallville',
                'emergency_contact' => 'Emma Thomas (+1-555-0218)',
            ],
            [
                'name' => 'Charlotte White',
                'email' => 'charlotte.white@medicare.test',
                'phone' => '+1-555-0219',
                'date_of_birth' => '1997-10-05',
                'gender' => 'female',
                'address' => '741 Ash Court, Starling',
                'emergency_contact' => 'Daniel White (+1-555-0220)',
            ],
        ];

        foreach ($patientsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $defaultPassword,
                    'role' => 'patient',
                    'phone' => $data['phone'],
                ]
            );

            Patient::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'address' => $data['address'],
                    'emergency_contact' => $data['emergency_contact'],
                ]
            );
        }
    }
}
