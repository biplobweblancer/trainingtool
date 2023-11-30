<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';

    protected $table = "trainings";
    protected $guarded = [];

    public function trainingTitle()
    {
        return $this->hasOne(TrainingTitle::class, 'id', 'titleId');
    }

    public function title()
    {
        return $this->belongsTo(TrainingTitle::class, 'titleId');
    }
}
