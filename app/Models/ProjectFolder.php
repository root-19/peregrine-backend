<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectFolder extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'parent_folder_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parentFolder(): BelongsTo
    {
        return $this->belongsTo(ProjectFolder::class, 'parent_folder_id');
    }

    public function subfolders(): HasMany
    {
        return $this->hasMany(Subfolder::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(FolderAssignment::class, 'folder_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'folder_assignments', 'folder_id', 'user_id');
    }

    public function procurement(): HasMany
    {
        return $this->hasMany(Procurement::class, 'folder_id');
    }
}
