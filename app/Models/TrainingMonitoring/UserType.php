<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_user_types";
    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class,'ProfileId','id');
    }

    public function district()
    {
        return $this->belongsTo(District::class,'district_id','id');
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class,'upazila_id','id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class,'provider_id','id');
    }
}
