<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'user_role';
    protected $fillable = ['role', 'additional_info','permission','status', 'created_by', 'updated_by'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
