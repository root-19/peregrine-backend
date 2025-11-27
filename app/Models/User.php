<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function projectAssignments(): HasMany
    {
        return $this->hasMany(\App\Models\ProjectAssignment::class);
    }

    public function folderAssignments(): HasMany
    {
        return $this->hasMany(\App\Models\FolderAssignment::class);
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(\App\Models\Project::class, 'project_assignments', 'user_id', 'project_id');
    }

    public function assignedFolders()
    {
        return $this->belongsToMany(\App\Models\ProjectFolder::class, 'folder_assignments', 'user_id', 'folder_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id')->where('sender_type', 'user');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id')->where('receiver_type', 'user');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'company_name',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
