<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingBatch extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "training_batches";
    protected $guarded = [];

    public function Provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function training()
    {
        return $this->hasOne(Training::class, 'id', 'trainingId');
    }

    public function trainingBatchSchedule()
    {
        return $this->hasOne(TrainingBatchSchedule::class);
    }

    public function getTraining()
    {
        return $this->belongsTo(Training::class, 'trainingId');
    }

    public function trainingApplicant()
    {
        return $this->hasOne(TrainingApplicant::class, 'BatchId');
    }

    public function providerTrainers()
    {
        return $this->hasMany(ProvidersTrainer::class, 'batch_id');
    }
    public function primaryTrainer()
    {
        return $this->hasOne(ProvidersTrainer::class, 'batch_id');
    }
    public function schedule()
    {
        return $this->hasOne(TrainingBatchSchedule::class, 'training_batch_id');
    }


    public function trainees()
    {
        return $this->hasMany(TrainingApplicant::class, 'BatchId')
            ->where('IsTrainee', 1);
    }
}
