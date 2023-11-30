<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrelimarySelection extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "tms_prelimary_selections";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
