<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function folders(): HasMany
    {
        return $this->hasMany(ProjectFolder::class);
    }

    public function subfolders(): HasMany
    {
        return $this->hasMany(Subfolder::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'project_assignments', 'project_id', 'user_id');
    }

    public function procurement(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }
}
