<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "geodistricts";
    protected $guarded = [];


    public function division()
    {
        return $this->belongsTo(Division::class, 'ParentCode', 'Code');
    }

    // public function upazila()
    // {
    //     return $this->belongsTo(Division::class, 'ParentCode', 'Code');

    // }


    public function upazilas()
    {
        return $this->hasMany(Upazila::class, 'ParentCode', 'Code');
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
