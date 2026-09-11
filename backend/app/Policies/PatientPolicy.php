<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

/**
 * PatientPolicy
 *
 * Security rules:
 * - Admin: can view all patients, update any patient.
 * - Doctor: can view any patient (limited fields via resource).
 * - Patient: can ONLY view and update THEIR OWN profile.
 *            Cannot access other patients' data.
 */
class PatientPolicy
{
    /**
     * Admin can see all patients list.
     * Doctors can see a list (relevant patients only, filtered in controller).
     * Patients cannot list all patients — they only access their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Admin: any patient.
     * Doctor: any patient.
     * Patient: ONLY their own record — never another patient's.
     */
    public function view(User $user, Patient $patient): bool
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        // Patient can only view their own profile
        return $user->isPatient() && $user->id === $patient->user_id;
    }

    /**
     * Only admin can create patient profiles directly (patients are created on registration).
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin: can update any patient.
     * Patient: can ONLY update their own profile — not another patient's.
     * Doctor: cannot update patient profiles.
     */
    public function update(User $user, Patient $patient): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isPatient() && $user->id === $patient->user_id;
    }

    /**
     * Only admin can delete (soft-delete) a patient.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->isAdmin();
    }
}
