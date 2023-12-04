<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';

    protected $table = "development_partners";
    protected $guarded = [];

    public function userType()
    {
        return $this->hasMany(UserType::class);
    }



    public function TrainingBatches()
    {
        return $this->hasMany(TrainingBatch::class);
    }
}
