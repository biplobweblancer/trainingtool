<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upazila extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "geoupazilas";
    protected $guarded = [];


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function district()
    {
        return $this->belongsTo(District::class,'ParentCode','Code');
    }

    public function userType()
    {
        return $this->hasMany(UserType::class);
    }

    public function userDetail()
    {
        return $this->hasMany(UserDetail::class);
    }
}
