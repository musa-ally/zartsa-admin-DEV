<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DualControlApproval extends Model
{
    use HasFactory;

    protected $fillable = ['approvers', 'status'];

    public function user(){
        return $this->belongsTo(User::class, 'requested_by', 'id');
    }

    public function task(){
        return $this->belongsTo(DualControlTask::class, 'dual_control_task_code', 'code');
    }
}
