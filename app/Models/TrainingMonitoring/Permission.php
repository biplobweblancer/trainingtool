<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';

    protected $table = "tms_permissions";
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tms_role_has_permissions', 'permission_id','role_id');
    }
}
