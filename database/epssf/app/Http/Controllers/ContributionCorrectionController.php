<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;

use Ceb\Http\Requests;
use Ceb\Models\Contribution;
use Ceb\Models\User;
use Ceb\Http\Controllers\Controller;
use Ceb\Factories\ContributionCorrectionFactory;

class ContributionCorrectionController extends Controller
{
    protected $factory;

    function __construct(ContributionCorrectionFactory $contributionCorrectionFactory)
    {
        parent::__construct();

        $this->factory = $contributionCorrectionFactory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->factory->contributions()->isEmpty()) {
            return view('corrections.contributions.find-transaction');
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
        $contributions = Contribution::with('member')->where('transactionid',request()->get('transactionid'))->get();

        if ($contributions->isEmpty()) {

            flash()->warning(trans('general.there_no_record_for_provided_transaction'));
            return $this->index();
        }
        $contributions = $contributions->transform(function($contribution){
                $member = $contribution->member;

                return [
                    'id'            => $contribution->id,
                    'amount'        => $contribution->amount,
                    'adhersion_id'  => (int) $contribution->adhersion_id,
                    'nid'           => $member->member_nid,
                    'institution'   => $member->institution->name,
                    'employee_id'   => $member->employee_id,
                    'names'         => $member->first_name.' '.$member->last_name,
                    'transactionid' => $contribution->transactionid,
                    'action'        => 'NONE',
                ];
        });

        $this->factory->setContributions($contributions);
        
        return $this->reload();
    }

    private function reload()
    {
        $contributions = $this->factory->contributions();

        return view('corrections.contributions.index',compact('contributions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($adhersion_id)
    {
        $this->factory->edit($adhersion_id);

        flash()->success(trans('general.item_updated'));

        return $this->reload();
    }

    /**
     * Remove item from the contributions session
     * @param   $id 
     * @return  
     */
    public function remove($adhersion_id)
    {
        $this->factory->remove($adhersion_id);

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
        $this->factory->add($request->adhersion_id,$request->amount);

        flash()->success(trans('general.contribution_add'));
        return $this->reload();
    }

    /**
     * Cancel current operation
     * @return  
     */
    public function cancel()
    {
        if ($this->factory->clear()) {
            flash()->success(trans('general.transaction_cancelled'));
        }

        return redirect()->route('corrections.contributions.index');
    }

    /**
     * complete current transactions and persist it in DB
     * @return  
     */
    public function complete()
    {
        $this->factory->complete();
        return $this->index();
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
