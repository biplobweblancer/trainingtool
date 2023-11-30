<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emailnotification extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_emailnotifications";
    protected $primaryKey = "id";

    protected $fillable = [
        'id',
        'email_id',
        'subject',
        'status',
        'user_id',
        'send_date'
    ];
    

   

    /**
     * Get the user email get.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
