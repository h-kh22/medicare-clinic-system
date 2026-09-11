<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Cardiology',
                'description' => 'Comprehensive diagnosis and treatment of heart and cardiovascular conditions.',
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Medical care for infants, children, and adolescents focusing on health and development.',
            ],
            [
                'name' => 'Dermatology',
                'description' => 'Specialized management of skin, hair, and nail diseases as well as cosmetic conditions.',
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Surgical and non-surgical treatment of the musculoskeletal system, bones, and joints.',
            ],
            [
                'name' => 'General Medicine',
                'description' => 'Primary care, disease prevention, and routine health evaluations for adults and families.',
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::firstOrCreate(
                ['name' => $specialty['name']],
                ['description' => $specialty['description']]
            );
        }
    }
}
