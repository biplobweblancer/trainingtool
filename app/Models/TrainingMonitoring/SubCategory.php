<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_sub_categories";
    protected $guarded = [];


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function userDetail()
    {
        return $this->hasMany(UserDetail::class);
    }
}
