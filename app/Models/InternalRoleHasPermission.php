<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalRoleHasPermission extends Model
{
    use HasFactory;
    protected $table = 'internal_user_roles_has_role_permissions';

    public function permission(){
        return $this->belongsTo(RolePermission::class, 'role_permissions_id');
    }
}
