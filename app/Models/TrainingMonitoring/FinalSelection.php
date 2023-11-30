<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalSelection extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "tms_final_selections";
    protected $guarded = [];

}