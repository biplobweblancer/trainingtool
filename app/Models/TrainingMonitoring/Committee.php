<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_committees";
    protected $guarded = [];


}