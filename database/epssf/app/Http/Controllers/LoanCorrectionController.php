<?php

namespace Ceb\Http\Controllers;

use Ceb\Models\Loan;
use Ceb\Http\Requests;
use Ceb\Models\Approval;
use Ceb\Models\UserGroup;
use Illuminate\Http\Request;
use Ceb\Http\Controllers\Controller;

class LoanCorrectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('corrections.loan.find-loan');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $transactionId = request()->get('transactionid');
        $loan = Loan::findByTransaction($transactionId)->first();
        
        if (empty($loan)) {
            flash()->warning(trans('loan.unable_to_find_loan_with_transaction_id',['transactionID' => $transactionId]));
            return $this->index();
        }

        $member                                  = $loan->member;
        $rightToLoan                             = $loan->right_to_loan;
        
        $loanInputs                              = $loan->toArray();
        $wording                                 = $loan->comment;
        $loanInputs['net_to_receive']            = $loan->amount_received;
        $loanInputs['net_to_receive']            = $loan->amount_received;
        $loanInputs['interest_on_urgently_loan'] = $loan->amount_received;

        $creditPostings  = $loan->postings->where('transaction_type','credit');
        $debitPostings   = $loan->postings->where('transaction_type','debit');
        $cautionneurs    = $loan->cautions;

        return view('corrections.loan.form',
                    compact(
                            'member',
                            'rightToLoan',
                            'loan',
                            'cautionneurs',
                            'loanInputs',
                            'wording',
                            'creditPostings',
                            'debitPostings'
                        )
                );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get administrators before you save
        $approvers = [];
        $approvalGroups = UserGroup::whereIn('name',['GERANT','Admins'])->get();
        foreach ($approvalGroups as $group) {
            $approvers[] = $group->users->pluck('id')->toArray();
        }

        $approvers = collect($approvers)->flatten();

        $approval = [ 
                    'transactionid' => $request->get('transactionid'),
                     'nature'    => 'loan',
                     'type'      => 'loan_correction',
                     'content'   => json_encode($request->all()),
                     'status'    => 'pending',
                     'approvers' => $approvers->toJson()
                 ];

        if (Approval::create($approval)) {
            flash()->success(trans('loan.adjusted_successfully_waiting_for_approval'));
            return $this->index();
        }        

        flash()->success(trans('loan.error_happened_while_adjusting_this_report'));
        return $this->show();
    }
}
