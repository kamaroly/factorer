<?php

namespace Modules\Accounting\Entities;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
USE App\Models\User;

class Accounting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    
    function setCreatedAtAttribute(){
        ////$this->received_at = Carbon::now()->format('Y-m-d H:i:s');
         $this->created_at = Carbon::now()->format('Y-m-d H:i:s');
    }



      /**
     * Get a client associated to this
     * order
     */
    public function user()
    {

        return $this->hasOne(User::class, "id", "userid");
    }

    public function users()
    {
        return $this->belongsTo(User::class,'userid','id');
    }

}
