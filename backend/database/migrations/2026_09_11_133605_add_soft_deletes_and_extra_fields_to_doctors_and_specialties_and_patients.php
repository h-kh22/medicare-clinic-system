<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds missing columns to doctors, specialties, and patients tables:
 * - soft_deletes on specialties, doctors, patients
 * - address, profile_image on doctors
 *
 * Uses Schema::hasTable() guard so SQLite in-memory test databases
 * (which run all migrations from scratch) are not affected.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── specialties ──
        if (Schema::hasTable('specialties') && ! Schema::hasColumn('specialties', 'deleted_at')) {
            Schema::table('specialties', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // ── doctors ──
        if (Schema::hasTable('doctors')) {
            Schema::table('doctors', function (Blueprint $table) {
                if (! Schema::hasColumn('doctors', 'address')) {
                    $table->string('address')->nullable()->after('consultation_fee');
                }
                if (! Schema::hasColumn('doctors', 'profile_image')) {
                    $table->string('profile_image')->nullable()->after('address');
                }
                if (! Schema::hasColumn('doctors', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // ── patients ──
        if (Schema::hasTable('patients') && ! Schema::hasColumn('patients', 'deleted_at')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('specialties') && Schema::hasColumn('specialties', 'deleted_at')) {
            Schema::table('specialties', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('doctors')) {
            Schema::table('doctors', function (Blueprint $table) {
                if (Schema::hasColumn('doctors', 'address')) {
                    $table->dropColumn('address');
                }
                if (Schema::hasColumn('doctors', 'profile_image')) {
                    $table->dropColumn('profile_image');
                }
                if (Schema::hasColumn('doctors', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }

        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'deleted_at')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
