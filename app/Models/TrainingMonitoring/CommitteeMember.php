<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_committee_members";
    protected $guarded = [];


}