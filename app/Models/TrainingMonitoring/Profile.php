<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';

    protected $table = "profiles";
    protected $hidden = ['DateOfBirth', 'LastActive', 'Introduction', 'BloodGroup', 'Religion', 'PassportNo', 'CreatedBy', 'Designation', 'MaritalStatus', 'Kids', 'JoiningDate', 'Phone2', 'SignatureUrl',];
    protected $guarded = [];
    public function coordinator()
    {
        return $this->hasOne(ProvidersBatchCoordinator::class, 'ProfileId', 'id');
    }
    
}
