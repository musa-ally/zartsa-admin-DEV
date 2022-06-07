<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalUsersHasInternalUserRole extends Model
{
    use HasFactory;

    protected $primaryKey = 'internal_users_id';
    protected $fillable = ['internal_users_account_status_code'];
}
