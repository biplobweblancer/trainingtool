<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "profiles";
    protected $guarded = [];

    public function coordinator()
    {
        return $this->hasOne(ProvidersBatchCoordinator::class, 'ProfileId', 'id');
    }

    public function trainerProfile()
    {
        return $this->hasMany(TrainerProfile::class, 'ProfileId', 'id');
    }
}
