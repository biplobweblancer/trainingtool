<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
