<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Write code on Method
 *
 * @return response()
 */
class Batch extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';

    protected $table = "training_batches";
    protected $guarded = [];
}
