<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Category extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';

    protected $table = "tms_categories";
    protected $guarded = [];

    public function userDetail()
    {
        return $this->hasMany(UserDetail::class);
    }
}
