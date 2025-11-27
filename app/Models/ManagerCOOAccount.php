<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ManagerCOOAccount extends Model
{
    use HasApiTokens;
    protected $table = 'manager_coo_accounts';

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'company_name',
        'position',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
