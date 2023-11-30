<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';

    protected $table = "tms_role_has_permissions";
    protected $guarded = [];

    public $timestamps = false;
}
