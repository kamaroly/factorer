<?php

namespace Modules\Accounting\Entities;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accounting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'postings';

    protected $casts = [
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    
  protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\PostingFactory::new();
    }

    function setCreatedAtAttribute(){
        $this->received_at = Carbon::now()->format('Y-m-d H:i:s');
         $this->created_at = Carbon::now()->format('Y-m-d H:i:s');
    }
}
