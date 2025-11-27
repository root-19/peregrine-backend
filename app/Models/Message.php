<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_type',
        'sender_id',
        'receiver_type',
        'receiver_id',
        'message',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sender account (polymorphic).
     */
    public function sender()
    {
        switch ($this->sender_type) {
            case 'hr':
                return $this->belongsTo(\App\Models\HRAccount::class, 'sender_id');
            case 'manager_coo':
                return $this->belongsTo(\App\Models\ManagerCOOAccount::class, 'sender_id');
            case 'user':
                return $this->belongsTo(\App\Models\User::class, 'sender_id');
            default:
                return null;
        }
    }

    /**
     * Get the receiver account (polymorphic).
     */
    public function receiver()
    {
        switch ($this->receiver_type) {
            case 'hr':
                return $this->belongsTo(\App\Models\HRAccount::class, 'receiver_id');
            case 'manager_coo':
                return $this->belongsTo(\App\Models\ManagerCOOAccount::class, 'receiver_id');
            case 'user':
                return $this->belongsTo(\App\Models\User::class, 'receiver_id');
            default:
                return null;
        }
    }
}
