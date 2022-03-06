<?php

namespace Modules\Order\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Client\Entities\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Order\Database\factories\OrderFactory::new();
    }

    /**
     * Get a client associated to this
     * order
     */
    public function client()
    {

        return $this->hasOne(Client::class, "id", "client_id");
    }

    public function getColorAttribute()
    {
        switch($this->status){
            case 'processing':
                return 'warning';
                break;
            case 'paid':
                return 'primary';
            case 'completed':
                return 'success';

            default:
                return 'secondary';
                break;
            }
    }

    /**
     * Get transactions
     *
     * @return void
     */
    public function transactions()
    {
        return self::select(
                DB::raw("
                order_transaction_id,
                SUM(quantity) as quantity,
                SUM(total_price) total_price,
                client_id,
                status,
                CASE
                    WHEN status = 'paid' then 'primary'
                    WHEN status = 'processing' THEN 'warning'
                    WHEN status = 'completed' THEN 'success'
                 END AS color,
                date(created_at) as created_at"))
                ->groupBy(
                    DB::raw(
                        "order_transaction_id,
                        client_id,
                        date(created_at),
                        status,
                        CASE
                            WHEN status = 'paid' then 'primary'
                            WHEN status = 'processing' THEN 'default'
                            WHEN status = 'completed' THEN 'success'
                        END"));
    }


}
