<?php

namespace Ceb\Http\Controllers;

use Ceb\Factories\ContributionFactory;
use Ceb\Factories\MemberTransactionsFactory;
use Ceb\Factories\RefundFactory;
use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests\AddNewMemberRequest;
use Ceb\Http\Requests\CompleteMemberTransactionRequest;
use Ceb\Http\Requests\EditMemberRequest;
use Ceb\Models\Contribution;
use Ceb\Models\DefaultAccount;
use Ceb\Models\Institution;
use Ceb\Models\Bank;
use Ceb\Models\Loan;
use Ceb\Models\User;
use Ceb\Repositories\Member\MemberRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Redirect;
use Response;

class MemberController extends Controller {
	public $member;
	function __construct(User $member) {
		$this->middleware('sentry.auth');
		$this->member = $member;
		parent::__construct();
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(MemberRepositoryInterface $memberRepository) {

		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.list')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}

		// First log
		Log::info($this->user->email . ' viewed member list');

		$members = $this->member;

		// Get members with emergency loan
		if (request()->has('emergencies')) {
			$members = $memberRepository->withEmergencies();
		}
		
		$members = $members->whereNotNull('adhersion_id')->whereNotNull('status')->where('adhersion_id','<>','200799999')->whereNull('deleted_at')->paginate(20);
		return view('members.list', compact('members'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Institution $institutions,Bank $banks) {
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.create')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}
		$member = $this->member;
		$institutions = $institutions->lists('name', 'id');
		$banks = $banks->lists('bank_name', 'id');

		return view('members.create', compact('member', 'institutions','banks'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(AddNewMemberRequest $request, MemberRepositoryInterface $memberRepository) {
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.create')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}
		$message = $memberRepository->store($request->all());
		flash()->success($message);
		return Redirect::route('members.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id,Bank $banks) {
			$banks = $banks->lists('bank_name', 'id');
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.view') && !$this->user->hasAccess('ceb.view.own.profile')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}

		$member = null;
		// If this user is ceb member then we show his own profile
		if ($this->user->isMember()) {
			$member = $this->member->findOrfail($this->user->id);

			return;
		}

		// If member is empty then this is an employee let's 
		// get information for whomever he is requesting 
		if (empty($member)) {
			$relations = ['loans','contributions','refunds','cautions','cautioned','attornies','institution','bank'];
			$member    = $this->member->with($relations)->findOrfail($id);
		}
         
		return view('members.edit', compact('member','banks'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, Institution $institutions ,Bank $banks) {
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.edit')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}

		$member       = $this->member->findOrfail($id);
		$institutions = $institutions->lists('name', 'id');

		$banks = $banks->lists('bank_name', 'id');

		return view('members.edit', compact('member', 'institutions','banks'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(EditMemberRequest $request, MemberRepositoryInterface $memberRepository, $id) {

		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.edit')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}

		// First prepare data
		$data       = (array) $request->all();
		$data['id'] = $id;

		// Attempt update
		$message = $memberRepository->update($data);

		flash()->success($message);

		return Redirect::route('members.edit', ['members' => $id]);
	}

	/**
	 * Contribution of  this member
	 * @param  int        $memberId unique ID of this member
	 * @param  RefundFactory $refund   
	 * @return redirect
	 */
	public function contribute($memberId,ContributionFactory $contribution)
	{
		if (!$contribution->setMember($memberId)) {
			return redirect()->back();
		}

	   return redirect()->route('contributions.index');
	}

	/**
	 * refund this member
	 * @param  int        $memberId unique ID of this member
	 * @param  RefundFactory $refund   
	 * @return redirect
	 */
	public function refund($memberId,RefundFactory $refund)
	{
		if (!$refund->setMember($memberId)) {
			return redirect()->back();
		}

	   return redirect()->route('refunds.index');
	}
	
	/**
  	 * Show member loan records
  	 * @param  numeric $memberId the ID of the member
  	 * @return view    
  	 */
    public function loanRecords(Loan $loan,$adhersionId,$excel=0)
    {
 		$startDate = date('Y-m-d', 0);
 		$endDate   = date('Y-m-d');
 		
    	return redirect()->to('reports/members/loanrecords/'.$startDate.'/'.$endDate.'/0/'.$adhersionId);
    }

    /**
     * Show this member contribution
     * @param  numeric $memberId [description]
     * @return view       
     */
    public function contributions(Contribution $contribution,$adhersionId)
    { 	
    	$startDate = date('Y-m-d', 0);
 		$endDate   = date('Y-m-d');

    	return redirect()->to('reports/members/contributions/'.$startDate.'/'.$endDate.'/0/'.$adhersionId);
    }

	/**
	 * This method shows transaction form
	 * @return view 
	 */
	public function transacts($memberId,$transactionid= null)
	{   

		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.transact')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}

		$member = $this->member->with(['loans','contributions','refunds','cautions','cautioned','attornies','institution'])->findOrfail($memberId);

		$movement_type  = Input::has('movement_type') ? Input::get('movement_type') : 'saving' ;
		$operation_type = Input::has('operation_type') ? Input::get('operation_type') :'individual_monthly_contribution' ;	

        $defaultAccounts = $this->getDefaultAccounts($movement_type,$operation_type);
		return view('members.transactions',compact('member','transactionid','movement_type','operation_type','defaultAccounts'));
	}
	/**
     * Get default accounts for this modules
     * @return array 
     */

    public function getDefaultAccounts($movement_type,$operation_type)

    {
    	$functionName = $movement_type.'.'.$operation_type;

        switch ($movement_type) {
            case 'saving':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->memberTransaction($functionName)->get();
				$defaultCreditsAccounts	=  DefaultAccount::with('accounts')->credit()->memberTransaction($functionName)->get();
                break;
            case 'withdrawal':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->memberTransaction($functionName)->get();
				$defaultCreditsAccounts	=  DefaultAccount::with('accounts')->credit()->memberTransaction($functionName)->get();
                break;   
            default:
             return [
		            'debits' => [],
		            'credits' => [],
		        ];
			break;

        }
        // dd($defaultDebitsAccounts);
        $debitsAccounts = [];
        $creditsAccounts = [];

		foreach ($defaultDebitsAccounts as $defaultDebitAccount) 
		{
			foreach ($defaultDebitAccount->accounts as $account) 
			{

				$debitsAccounts[$account->id]	= $account->account_number.' '. $account->entitled;
			}
		}
	

		foreach ($defaultCreditsAccounts as $defaultCreditAccount) 
		{
            foreach ($defaultCreditAccount->accounts as $account) 
            {
                $creditsAccounts[$account->id] = $account->entitled;
            }
        }
        

        return [
            'debits' => (object) $debitsAccounts,
            'credits' => (object) $creditsAccounts
        ];

    }

	/**
	 * This method complete transaction
	 * @return redirect
	 */
	public function completeTransaction($memberId,
										CompleteMemberTransactionRequest $request,
										MemberTransactionsFactory $factory,
										Bank $banks)
	{
		 $data = $request->all();
		 
		 $data['member'] = $this->member->findOrfail($memberId);
		 $transactionid = null;
		 if (($transactionid = $factory->complete($data)) != false) {
		 	flash()->success(trans('member.transaction_well_recorded'));
		 	
		 	return $this->show($memberId,$banks);
		 }

		 // Error happened here
		 return  $this->transacts($memberId);
	}
    
    /**
	 * This method shows transaction form
	 * @return view 
	 */
	public function attornies($memberId)
	{
		$member = $this->member->findOrfail($memberId);
		$title = trans('member.member_attornies_list') ;
		return view('members.attornies',compact('member','title'));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(MemberRepositoryInterface $memberRepository, $id) {
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.delete')) {
			flash()->error(trans('Sentinel::users.noaccess'));

			return redirect()->back();
		}
		$user = $this->member->findOrfail($id);
		// 1. Check loans first
		if ($user->haas_active_loan) {
			flash()->error(trans('member.this_member_still_has_active_loan_therefore_you_cannot_remove_him'));
			return redirect()->back();
		}
		
		// 2. Savings
		if ($user->total_contribution) {
			flash()->error(trans('member.this_member_still_has_contribution_therefore_you_cannot_remove_him'));
			return redirect()->back();
		}

		// 3. cautionneurs
		if ($user->cautionBalance > 0) {
			flash()->error(trans('member.this_member_still_has_contribution_therefore_you_cannot_remove_him'));
			return redirect()->back();
		}

		// Delete the user
		if ($user->delete()) {
			//Fire the sentinel.user.destroyed event
			flash()->success(trans('Sentinel::users.destroyed'));
		}

		return Redirect::route('members.index');
	}

	/**
	 * Search method for member
	 * @param  MemberRepository $member
	 * @param  Request          $request
	 * @return [type]
	 */
	public function search(MemberRepositoryInterface $member, Request $request) {
        
		$results = $member->search($request->input('query'));
      
		return Response::json($results);
	}

	/**
	 * Get current member cautions
	 * @param  numeric $id 
	 * @return 
	 */
	public function currentCautions($id)
	{
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.view.current.i.cautioned')) {
			return trans('Sentinel::users.noaccess');
		}

		$title    = trans('member.i_have_cautionned');
		$cautions = [];
		$member   = $this->member->find($id);
		$type     = 'me';

		if (!is_null($member)) {
			$cautions = $member->cautioned_medash;
		}
		// dd($cautions);
		
		return view('members.cautionneurs.list',compact('cautions','title','type'));
	}

	/**
	 * Show members I am currently cautionning
	 * @param  id $value 
	 * @return 
	 */
	public function currentCautionedByMe($id)
	{
		// First check if the user has the permission to do this
		if (!$this->user->hasAccess('member.view.current.cautioned.by.me')) {
			return trans('Sentinel::users.noaccess');
		}

		$cautions = [];
		$member   = $this->member->find($id);
		$title 	  = trans('member.member_who_cautionned_me');
		$type     = 'by_me';

		if (!is_null($member)) {
			$cautions = $member->current_cautions;
		}

		return view('members.cautionneurs.list',compact('cautions','title','type'));
	}

	/**
	 * Show current logged in member notifications
	 * 		
	 * @return view
	 */
	public function notificatons()
	{
		return view('partials.user-all-notifications');
	}
}
