<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medicine_name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
    ];

    /**
     * Get the prescription this line item belongs to.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
