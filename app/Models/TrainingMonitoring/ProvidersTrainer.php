<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvidersTrainer extends Model
{
        use HasFactory;
        protected $connection = 'mysql-soms';

        protected $table = "tms_providers_trainers";
        protected $guarded = [];


        public function profile()
        {
                return $this->belongsTo(Profile::class, 'ProfileId', 'id');
        }

        public function trainingBatch()
        {
                return $this->belongsTo(TrainingBatch::class, 'batch_id', 'id');
        }

        public function provider()
        {
                return $this->belongsTo(Provider::class, 'provider_id', 'id');
        }
}
