<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Posting extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\PostingFactory::new();
    }

    public function debit(){
    	return $this->belongsTo(Account::class,'debit_account_id','account_number');
    }
    
     public function credit(){
    	return $this->belongsTo(Account::class,'credit_account_id','account_number');
    }

}
