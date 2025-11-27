<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subfolder extends Model
{
    protected $fillable = [
        'project_folder_id',
        'project_id',
        'name',
        'button_name',
    ];

    public function projectFolder(): BelongsTo
    {
        return $this->belongsTo(ProjectFolder::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
