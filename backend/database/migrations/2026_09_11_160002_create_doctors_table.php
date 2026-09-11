<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('restrict');
            $table->string('license_number')->unique();
            $table->text('bio')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->timestamps();
            $table->string('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
