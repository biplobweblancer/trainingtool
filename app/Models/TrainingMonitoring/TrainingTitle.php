<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingTitle extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "training_titles";
    protected $guarded = [];


    public function training()
    {
        return $this->belongsTo(Training::class);
    }

}
