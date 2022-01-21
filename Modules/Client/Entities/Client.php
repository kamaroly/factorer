<?php

namespace Modules\Client\Entities;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    protected $guarded = [];

    protected $casts = [
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    


    function setCreatedAtAttribute(){
        $this->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $this->updated_at = Carbon::now()->format('Y-m-d H:i:s');
       $this->client_id = '2022002';
    }
}
