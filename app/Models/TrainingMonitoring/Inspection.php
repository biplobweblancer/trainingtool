<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';
    protected $table = "tms_inspections";
    protected $guarded = ['id'];

    public function batch()
    {
        return $this->belongsTo(TrainingBatch::class, 'batch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

}