<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by_id',
        'requested_by_name',
        'requested_by_position',
        'department',
        'date_of_request',
        'date_needed',
        'project_name',
        'project_location',
        'purpose',
        'materials',
        'priority',
        'status',
        'remarks',
        'rejection_reason',
        'approved_by_id',
        'approved_by_type',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'date_of_request' => 'date',
        'date_needed' => 'date',
        'materials' => 'array',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who requested the materials
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }
}

