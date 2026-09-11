<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with complete MediCare demo dataset.
     */
    public function run(): void
    {
        $this->call([
            SpecialtySeeder::class,
            UserSeeder::class,
            AppointmentSeeder::class,
            MedicalRecordSeeder::class,
            PrescriptionSeeder::class,
        ]);
    }
}
