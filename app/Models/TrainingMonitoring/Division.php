<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "geodivisions";
    protected $guarded = [];

    public function districts()
    {
        return $this->hasMany(District::class, 'ParentCode', 'Code');
    }

}