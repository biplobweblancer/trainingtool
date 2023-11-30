<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingApplicant extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "training_applicants";
    protected $guarded = [];


    public function profile()
    {
        return $this->hasOne(Profile::class, 'id', 'ProfileId');
    }

    public function trainingBatch()
    {
        return $this->hasOne(TrainingBatch::class, 'id', 'BatchId');
    }

    public function trainingTitle()
    {
        return $this->hasOne(TrainingTitle::class, 'id', 'TrainingTitleId');
    }

    public function getProfile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId');
    }
}
