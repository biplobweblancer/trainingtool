<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerProfile extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "trainer_profiles";
    protected $guarded = [];


    public function profile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId', 'id');
    }
}
