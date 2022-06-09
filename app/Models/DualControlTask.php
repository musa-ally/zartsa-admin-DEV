<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DualControlTask extends Model
{
    use HasFactory;

    protected $fillable = ['approvers', 'is_active'];
}
