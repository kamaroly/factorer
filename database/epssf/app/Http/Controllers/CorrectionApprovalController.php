<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;
use Ceb\Models\Approval;
use Ceb\Http\Requests;
use Ceb\Http\Controllers\Controller;

class CorrectionApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $approvals = Approval::notApproved()->notRejected()
                             ->orderBy('created_at','DESC')->paginate(20);
        return view('corrections.approvals.index',compact('approvals'));
    }

    /**
     * Change status of the approval
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id,$status)
    {
        $approval = Approval::find($id);
        
        if($approval->changeStatus($status)){
            flash()->success(trans('general.transaction_has_been_processed_successfully', $replace = ['id' => $id,'status'=>$status]));
        }

        return $this->index();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $approval = Approval::find($id);
        $content  = collect(json_decode($approval->content,true));
        $content  = $content->only(['transactionid',
                                    'adhersion_id',
                                    'movement_nature',
                                    'operation_type',
                                    'letter_date',
                                    'right_to_loan',
                                    'wished_amount',
                                    'loan_to_repay',
                                    'interests',
                                    'InteretsPU',
                                    'amount_received',
                                    'tranches_number',
                                    'monthly_fees',
                                    'cheque_number',
                                    'bank_id',]);

        $content  = jsonToTable($content->toArray());
        return view('corrections.approvals.show',compact('approval','content'));
    }
}
