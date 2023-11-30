<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_user_educations";
    protected $guarded = [];
   
}
