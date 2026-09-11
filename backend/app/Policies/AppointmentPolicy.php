<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor() || $user->isPatient();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin()
            || ($user->isDoctor() && $user->doctor?->id === $appointment->doctor_id)
            || ($user->isPatient() && $user->patient?->id === $appointment->patient_id);
    }

    public function create(User $user): bool
    {
        return $user->isPatient();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin()
            || ($user->isDoctor() && $user->doctor?->id === $appointment->doctor_id)
            || ($user->isPatient() && $user->patient?->id === $appointment->patient_id && $appointment->status === 'pending');
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin()
            || ($user->isDoctor() && $user->doctor?->id === $appointment->doctor_id)
            || ($user->isPatient() && $user->patient?->id === $appointment->patient_id && in_array($appointment->status, ['pending', 'confirmed'], true));
    }
}
