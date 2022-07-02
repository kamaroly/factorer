<?php

namespace Modules\Accounting\Http\Controllers\Backend;


use DB;
use Log;
use App\Http\Controllers\Controller;
use Modules\Accounting\Models\Posting;

class PostingsController extends Controller
{
    /**
     * Display the list of available postings order
     * by the latest created transactions
     */
    public function index()
    {
        $postings = Posting::select(
                        DB::raw('
                            transactionid,
                            wording,
                            MIN(amount) amount,
                            max(created_at) created_at'
                        ))
                    ->orderBy('created_at', 'desc')
                    ->groupBy(DB::raw('transactionid, wording'))
                    ->paginate(20);

        return view('accounting::backend.postings.index',[
            'postings' => $postings
        ]);
    }

}
