<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
USE App\Models\User;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
            'updated_at' => 'date',
            'created_at' => 'date',
        ];


    function setCreatedAtAttribute(){
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

    /**
     * Get purchase between
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
    }

    /**
     * Get records before
     */
    public function scopeBefore($query, $date)
    {
        return $query->where(DB::raw('DATE(created_at)'), '<', $date);
    }
}
