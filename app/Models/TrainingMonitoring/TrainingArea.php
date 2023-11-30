<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingArea extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "training_areas";
    protected $guarded = [];

}
