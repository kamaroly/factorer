<?php

namespace Modules\Receivings\Entities;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receiving extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
            'updated_at' => 'date',
        ];
    
    protected static function newFactory()
    {
        return \Modules\Receivings\Database\factories\ReceivingFactory::new();
    }

    function setCreatedAtAttribute(){
        $this->received_at = Carbon::now()->format('Y-m-d H:i:s');
    }
}
