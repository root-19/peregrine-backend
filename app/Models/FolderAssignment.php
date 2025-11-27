<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FolderAssignment extends Model
{
    protected $table = 'folder_assignments';

    protected $fillable = [
        'folder_id',
        'user_id',
        'assigned_at',
    ];

    public $timestamps = false;

    public function folder(): BelongsTo
    {
        return $this->belongsTo(ProjectFolder::class, 'folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
