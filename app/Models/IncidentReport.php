<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reported_by_id',
        'reported_by_name',
        'reported_by_position',
        'date_of_report',
        'location',
        'date_of_incident',
        'time_of_incident',
        'time_period',
        'description_of_accident',
        'is_someone_injured',
        'injury_description',
        'people_involved',
        'status',
        'resolution',
        'reviewed_by_id',
        'reviewed_by_type',
        'reviewed_at',
        'resolved_at',
    ];

    protected $casts = [
        'date_of_report' => 'date',
        'date_of_incident' => 'date',
        'is_someone_injured' => 'boolean',
        'people_involved' => 'array',
        'reviewed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
}

