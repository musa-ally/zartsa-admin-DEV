<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalUserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'service_status_code'
    ];

    public function rolePermissions(){
        return $this->hasMany(InternalRoleHasPermission::class, 'internal_user_roles_id');
    }
}
