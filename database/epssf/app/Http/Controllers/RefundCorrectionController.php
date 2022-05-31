<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;

use Ceb\Models\User;
use Ceb\Models\Refund;
use Ceb\Http\Requests;
use Ceb\Events\RefundDeleted;
use Ceb\Events\RefundAdjusted;
use Ceb\Http\Controllers\Controller;
use Ceb\Factories\RefundCorrectionFactory;

class RefundCorrectionController extends Controller
{   
    protected $factory;

    function __construct(RefundCorrectionFactory $refundCorrectionFactory)
    {
        parent::__construct();
        $this->factory = $refundCorrectionFactory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->factory->getRefunds()->isEmpty()) {
            return view('corrections.refunds.index');
        }
        return $this->reload();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // 1. Get all refund for this transactions
        $refunds = Refund::with('member')->byTransaction(request()->get('transactionid'))->get();

        if ($refunds->isEmpty()) {
            flash()->warning(trans('general.there_no_record_for_provided_transaction'));
            return $this->index();
        }
        // 2.Get important data to use for update
        $correctRefund = $refunds->transform(function($refund){
                            // We have no member attached to this refund
                            // Don't consider it.
                            if ($refund->member->isEmpty()) {
                                  return [];
                            }
                            
                            $member = $refund->member->first();
                            return [
                                'id'            => $refund->id,
                                'amount'        => $refund->amount,
                                'adhersion_id'  => $refund->adhersion_id,
                                'nid'           => $member->member_nid,
                                'institution'   => $member->institution->name,
                                'employee_id'   => $member->employee_id,
                                'names'         => $member->first_name.' '.$member->last_name,
                                'transactionid' => $refund->transaction_id,
                                'action'        => 'NONE',
                            ];
            })->filter(function($refund){
                return !empty($refund);
            });

        $this->factory->setRefunds($correctRefund);
        return $this->reload();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $refunds          = $this->factory->getRefunds();
        // Get the key to replace with
        $index            = $refunds->where('id', (int) $id)->keys()->first();
        $refund           = $refunds->where('id', (int) $id)->first();
        $refund['amount'] = request()->get('amount');
        $refund['action'] = 'UPDATE';
        // Update content of the session
        $refunds[$index] = $refund;
        $this->factory->setRefunds($refunds);

        return $this->reload();
    }

    /**
     * Remove item from the refunds session
     * @param   $id 
     * @return  
     */
    public function remove($id)
    {
        $refunds          = $this->factory->getRefunds();
        // Get the key to replace with
        // Get the key to replace with
        $index            = $refunds->where('id', (int) $id)->keys()->first();
        $refund           = $refunds->where('id', (int) $id)->first();
        $refund['action'] = 'REMOVE';
        $refunds[$index]  = $refund;
 
         $this->factory->setRefunds($refunds);
         flash(trans('Refund for '.$refund['names'].'('.$refund['adhersion_id'].') of '.number_format($refund['amount']).' Removed successfully'));
         return $this->index();
    }

    /**
     * Reload the page
     * @return  
     */
    public function reload()
    {
        $refunds = $this->factory->getRefunds();
        $transactionid = $refunds->first()['transactionid'];

        // 2. Display them for correction
        return view('corrections.refunds.form',compact('refunds','transactionid'));
    }

    /**
     * Complete refund correction
     * @return  
     */
    public function complete()
    {
        $refunds = $this->factory->getRefunds();
 
        // Make a copy of the repo
          $existingRefund_transaction        = Refund::where('transaction_id','=',$refunds->first()['transactionid'])                                 
                              ->first();

           $existingRefund_member        = Refund::where('adhersion_id','=',$refunds->first()['adhersion_id'])
                              ->first();
              /// dd($existingRefund1,$existingRefund2);
          $newRefund             = $existingRefund_transaction->replicate();
          $newRefund->created_at = $existingRefund_transaction->created_at;
          $newRefund->loan_id    = $existingRefund_member->loan_id;
          $newRefund->member_id  = $existingRefund_member->member_id;
           $newRefund->wording      = $existingRefund_transaction->wording;
        $refunds->each(function($refund) use($newRefund) {
            // Get fresh copy from database
            $refundInDb = Refund::find($refund['id']);     
            // Go to next if we don't have this in our DB
            if(empty($refundInDb) && $refund['action'] !== 'ADD'){
                return false;
            }
            // Determine what to do on this refund
            switch ($refund['action']) {
                case 'REMOVE':
                    $refundInDb->delete();
                    break;
                case 'ADD': # This didn't exist we are adding it

                    $newRefund->contract_number  = $refund['contract_number'];
                    $newRefund->amount       = $refund['amount'];
                    $newRefund->adhersion_id = $refund['adhersion_id'];
                   
                    $newRefund->save();
                    event(new RefundAdjusted($refundInDb));
                    break;
                case 'UPDATE':
                    $refundInDb->amount = $refund['amount'];
                    $refundInDb->wording      = 'REFUND_CORRECTED_BY_'.$this->user->email;
                    $refundInDb->save();

                    event(new RefundAdjusted($refundInDb));
                    break;
            }
        });

        // Remove this from session
        $this->factory->clear();
        
        flash()->success(trans('general.refund_adjusted'));
        return $this->index();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $refundsToCorrect = $this->factory->getRefunds();

        // 1. Don't add this adhersion if it is already in the session
        if (empty($refundsToCorrect->where('adhersion_id',$request->adhersion_id)->first()) === false) {
            flash()->warning(trans('general.adhersion_id_already_exists_for_this_transaction', ['adhersion_id' => $request->adhersion_id]));
            return $this->reload();
        }

        // 2. Confirm that adhersion number exists.
        $member = User::byAdhersion($request->adhersion_id)->first();

        if (! $member->exists() ) {
            // 2. If exists, then add it to the existing session with flag of new.
            flash()->error(trans('general.invalid_adhersionid', ['adhersion_id' => $request->adhersion_id]));
            return $this->reload();
        }


        $refundsToCorrect->prepend([
                    'id'            => null,
                    'amount'        => $request->amount,
                    'adhersion_id'  => $request->adhersion_id,
                    'nid'           => $member->member_nid,
                    'institution'   => $member->institution->name,
                    'employee_id'   => $member->employee_id,
                    'names'         => $member->first_name.' '.$member->last_name,
                    'transactionid' => $refundsToCorrect->first()['transactionid'],
                    'contract_number'=>$request->contract_number,
                    'action'        => 'ADD',
                ]);


        // Add this to the session
        $this->factory->setRefunds($refundsToCorrect);
        
        flash()->success(trans('general.refund_added'));

        return $this->reload();
    }

    public function cancel()
    {
        if ($this->factory->clear()) {
            flash()->success(trans('general.transaction_cancelled'));
        }

        return redirect()->route('corrections.refund.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
            $this->factory->removeTransaction();
            // Return index after deleting from DB
            return $this->index();
    }
}
