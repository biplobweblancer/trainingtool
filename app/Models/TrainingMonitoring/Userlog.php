<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userlog extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';
    protected $table = "tms_userlogs";
    protected $primaryKey = "id";

    protected $fillable = [
        'login_date',
        'logout_date',
        'status',
        'user_id'
    ];
    

      /**
     * Write code on Method
     *
     * @return response()
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

      /**
     * Get the User Details .
     */
    public function userDetail()
    {
        return $this->hasMany(UserDetail::class,'user_id','id');
        
    }
    
}
