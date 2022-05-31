<?php

namespace Ceb\Http\Controllers;

use Ceb\Factories\LoanFactory;
use Ceb\Http\Controllers\Controller;
use Ceb\Models\Account;
use Ceb\Models\Contribution;
use Ceb\Models\Institution;
use Ceb\Models\Loan;
use Ceb\Models\MemberLoanCautionneur;
use Ceb\Models\MonthlyFeeInventory;
use Ceb\Models\Posting;
use Ceb\Models\Refund;
use Ceb\Models\User;
use Ceb\Repositories\Reports\GraphicReportRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use stdClass;
use Carbon\Carbon;

class ReportController extends Controller {
	
	public $report;
	
	public $labels;
	
	function __construct(User $member) {
		$this->report = trans('report.nothing_to_show');
		$this->member = $member;
		$this->setLabels();
		parent::__construct();
	}

	public function index(GraphicReportRepository $report)
	{
	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.index')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing reports charts index');
		
		$contributions    = $report->getMonthlyContribution();
		
		$loans            = $report->getMontlyLoan();
		$institutions     = $report->getCountMemberPerInstitution();
		$institutionsLoan = $report->getLoanByInstitution();

		return view('reports.index',compact('contributions','loans','institutions','institutionsLoan'));
	}
      
	/**
	 * Show saving contract letter
	 * @param  $memberId ID of the member we are reporting for
	 * @return
	 */
	public function contractSaving($memberId) {
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.contract.saving')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing report contract saving');
    	
		$member = $this->member->byAdhersion($memberId)->first();

		if (is_null($member)) {
			flash()->error(trans('member.member_not_found'));
			return redirect()->route('reports.index');
		}

		 $contract = view('reports.contracts_saving')->render();
		 $contract = str_replace('{contract_id}',$member->contract_id,$contract);
		 $contract = str_replace('{names}',$member->names,$contract);
		 $contract = str_replace('{adhersion_id}',$member->adhersion_id,$contract);
		 $contract = str_replace('{institution}',$member->institution_name,$contract);
		 $monthly_fees =(int)  $member->monthly_fee;
		 $contract = str_replace('{monthly_fee_in_words}',convert_number_to_words($monthly_fees),$contract);
		 $contract = str_replace('{monthly_fee}',$monthly_fees,$contract);
		 $contract = str_replace('{created_at}',$member->updated_at->format('m-Y'),$contract);
		 $contract = str_replace('{today_date}',date('d-m-Y'),$contract);
		 $contract = str_replace('{institution_name}',$member->institution_name,$contract);
		 $contract = str_replace('{district}',$member->district,$contract);
		 $contract = str_replace('{province}',$member->province,$contract);
		 $contract = str_replace('{member_nid}',$member->member_nid,$contract);
		 $contract = str_replace('{President}',(new \Ceb\Models\Setting)->get('general.president'),$contract);

		 $report   = $contract;

 	   if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
	}
	/**
	 * Get loan contract number
	 * @param  $memberId
	 * @return mixed
	 */
	public function contractLoan(User $user,$identifier) {
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.contract.loan')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }
        
        // First log 
        Log::info($this->user->email . ' is viewing report contract loan');
       
        // We could not find the user using his Id, we assume, the 
    	// the provided identifier is a adhersion id  let's try 
    	// to look for him/her using his adhersion number
    	if (is_null($foundUser = $user->with('loans')->byAdhersion($identifier)->first())) {
    		flash()->error(trans('member.we_could_not_find_the_member_you_are_looking_for'));
    		Log::error('Unable to find a member with identifier'.$identifier);
    		return redirect()->back();
    	}
         

        // now we have found the member, let's try get his loan, otherwise we 
        // will display an error
        if (is_null($loan = $foundUser->latestLoanWithEmergency())) {

        	    flash()->error(trans('member.member_you_are_looking_for_does_not_have_a_loan_contract'));
        		Log::error('The member you are looking for does not have a loan contract:'.$identifier);
        		return redirect()->back();
        }

		// if the contract is empty, we assume it is not generated, let's try to generate it and save it
		if ($loan->status =='approved') {
			$loan->contract = generateContract($foundUser,strtolower($loan->operation_type));
			$loan->save();
		}
		
		// Handles french and other special characters
		$report = htmlentities_keepHtmlTags($loan->contract);

		 if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
 		return view('layouts.printing', compact('report'));
	}

	/**
	 * Get member by his ID
	 * @param  INTEGER $memberId 
	 * @return Model
	 */
	private function getMember($memberId) {
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.member')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing report member');
    
		// Do we have the member we are looking for ?
		if (($member = $this->member->find($memberId)) == null) {
			flash()->error(trans('member.member_not_found'));
			return false;
		}
		return $member;
	}

	/**
	 * Show accounting piece reports
	 * 
	 * @param  Account $account   
	 * @param  string  $startDate 
	 * @param  string  $endDate   
	 * @return view             
	 */
	public function accountingPiece(Posting $posting,$startDate=null,$endDate=null,$excel=0)
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.accounting.piece')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing report of accounting piece');
    
		$postings = $posting->with('account')->betweenDates($startDate,$endDate)->get();
		$report= view('reports.accounting.piece',compact('postings'))->render();
		if ($excel==1) {
		  toExcel($report,'account-piece-report');
		}         
		if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
	}

	/**
	 * Show ledge  reports
	 * 
	 * @param  Ceb\Models\Posting $posting   
	 * @param  string  $startDate 
	 * @param  string  $endDate   
	 * @return view             
	 */
	public function ledger(Posting $posting, $startDate=null,$endDate=null,$accountid=null,$excel=0)
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.ledger')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing ledger report');
    	

	    // Get Balance information fort his later
		$credits        = $posting->before($startDate)->where('account_id',$accountid)->credits()->sum('amount');
		$debits         = $posting->before($startDate)->where('account_id',$accountid)->debits()->sum('amount');

		$postings = $posting->with('account')
							->betweenDates($startDate,$endDate)
							->where('account_id',$accountid)
							->orderBy('id')->get();

		$report  = view('reports.accounting.ledger',compact('postings','credits','debits'))->render();
	    
	    if ($excel==1) {
			 toExcel($report,'ledger-report');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
        }

		return view('layouts.printing', compact('report'));
	}

	/**
	 * Show bilan report
	 * @param  Ceb\Models\Account $account   
	 * @param  string  $startDate 
	 * @param  string  $endDate   
	 * @return mixed             
	 */
	public function bilan(Account $account,$endDate=null,$excel=0)
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.bilan')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing bilan report');

        $report = view('reports.accounting.bilan')->render();
        
        /** @var Generate table for ACTIVE */
		$accounts = $account->with('postings')
							->where(DB::raw('LOWER(account_nature)'),strtolower('ASSET'))
							->orderBy('account_number','ASC')->get();
		
		$accountType =  'actif';						
		$actifs      = view('reports.accounting.bilan_item',compact('accounts','accountType'))->render();
		$actifTotal  = session()->get('total');
		$total       = $actifTotal;
		$total       = view('reports.accounting.bilan_total',compact('total'))->render();
		$actifs      = str_replace('BILAN_TOTAL', $total, $actifs);
		$actifs      = str_replace('BILAN_RESULTS', '', $actifs);
		
		/** @var string get passif  */
		$accounts    = $account->with('postings')
								->where(DB::raw('LOWER(account_nature)'),strtolower('LIABILITIES'))
				               ->orderBy('account_number','ASC')->get();
		$accountType =  'passif';
		$passif      = view('reports.accounting.bilan_item',compact('accounts','actifTotal','accountType'))->render();
		$passifTotal = session()->get('total');
		$balance     = $actifTotal - $passifTotal;
		$total       = $passifTotal + $balance;

		$resultant = view('reports.accounting.bilan_resultant',compact('balance'))->render();		
		$total     = view('reports.accounting.bilan_total',compact('total'))->render();
		$passif    = str_replace('BILAN_TOTAL', $total, $passif);
		$passif    = str_replace('BILAN_RESULTS', $resultant, $passif);

		/** @var string get charges  */
		$accounts = $account->with('postings')
							->where(DB::raw('LOWER(account_nature)'),strtolower('EXPENDITURE'))
							->orderBy('account_number','ASC')->get();
		
		$accountType =  'charges';				
		$charges = view('reports.accounting.bilan_item',compact('accounts','accountType'))->render();
		$chargesTotal =  session()->get('total');

		/** @var string get produit  */
		$accounts = $account->with('postings')
							->where(DB::raw('LOWER(account_nature)'),strtolower('INCOME'))
							->orderBy('account_number','ASC')->get();
		$accountType =  'produits';	
		$produits = view('reports.accounting.bilan_item',compact('accounts','accountType'))->render();
		$produitTotal =  session()->get('total');

		$balance     = $produitTotal - $chargesTotal;
		$total       = $chargesTotal + $balance;

		$resultant = view('reports.accounting.bilan_resultant',compact('balance'))->render();		
		$total     = view('reports.accounting.bilan_total',compact('total'))->render();
		$charges   = str_replace('BILAN_TOTAL', $total, $charges);
		$charges   = str_replace('BILAN_RESULTS', $resultant, $charges);

		$total    = $produitTotal;
		$total    = view('reports.accounting.bilan_total',compact('total'))->render();
		$produits = str_replace('BILAN_TOTAL', $total, $produits);
		$produits = str_replace('BILAN_RESULTS', '', $produits);



		// Clear session with total
		session()->forget('total');

		// POSITION REPORTS IN THE TABLE 
		$report = str_replace('ACTIF_TABLE', $actifs, $report);
		$report = str_replace('PASSIF_TABLE', $passif, $report);
		$report = str_replace('CHARGES_TABLE', $charges, $report);
		$report = str_replace('PRODUIT_TABLE', $produits, $report);

		if ($excel==1) {
			 toExcel($report,'bilan-report');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
	}

	/**
	 * Show ledge  reports
	 * 
	 * @param  Ceb\Models\Posting $posting   
	 * @param  string  $startDate 
	 * @param  string  $endDate   
	 * @return view             
	 */
	public function journal(Posting $posting, $startDate=null,$endDate=null,$excel=0)
	{
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.journal')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing journal report');
	
		$postings       = $posting->with('account')->betweenDates($startDate,$endDate)->get();
		$report         = view('reports.accounting.journal',compact('postings'))->render();

		if ($excel==1) {
			 toExcel($report,'journal-report');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
	}

	/**
	 * Show accounts list report
	 * @param  Ceb\Models\Account $account   
	 * @param  string  $startDate 
	 * @param  string  $endDate   
	 * @return mixed             
	 */
	public function accountsList(Account $account,$excel=0)
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.accounts.list')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing accounts list report');
    
		$accounts = $account->all();
		$report = view('reports.accounting.accounts-list',compact('accounts'))->render();
		if ($excel==1) {
			 toExcel($report,'journal-report');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
	}


  	/**
  	 * Show member loan records
  	 * @param  numeric $memberId the ID of the member
  	 * @return view    
  	 */
    public function loanRecords(Loan $loan, $startDate=null,$endDate=null,$excel=0,$adhersionId)
    { 
    	// Abort if member is tricking the system
    	$this->user->abortIfMemberTricksSystem($adhersionId);

	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.loans.records')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing loans records report');
 

    	$results = $loan->getLoanRecords($adhersionId,request()->segment(4),request()->segment(5));

		$loans               = $results['loans'];
		$previousLoanBalance = $results['previous_blance'];
		
	    $report = view('reports.member.loan_records',compact('loans','previousLoanBalance'))->render();

	    if ($excel==1) {
			 toExcel($report,'loanRecords-report');
		}
         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
    }

    
    /**
     * Monthly fee inventory history
     * @param  string  $startDate   
     * @param  string  $endDate     
     * @param  integer $excel       
     * @param  string  $adhersionId 
     * @return string               
     */
    public function monthlyFeeInventory($startDate=null,$endDate=null,$excel=0,$adhersionId=null)
    { 
	    // First check if the user has the permission to do this
       /** if (!$this->user->hasAccess('reports.member.montly.fee.inventory')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }**/

        // First log 
        Log::info($this->user->email . ' is viewing Member monthly fee inventory report');
 
    	$history = MonthlyFeeInventory::history($startDate,$endDate);
	    
	    $report = view('reports.member.monthly-fee-inventory',compact('history'))->render();

	    if ($excel==1) {
			 toExcel($report,'Monthly-fee-inventory-report');
		}
         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
    }

     /**
     * Monthly fee inventory history
     * @param  string  $startDate   
     * @param  string  $endDate     
     * @param  integer $excel       
     * @param  string  $adhersionId 
     * @return string               
     */
    public function octroye($startDate=null,$endDate=null,$institution=11,$excel=0)
    { 
	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.loans.records')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
                // First log 
        Log::info($this->user->email . ' is viewing octroye report');
		$loans = Refund::octroye($startDate,$endDate,$institution);

		$report =view('reports.loans.octroye',compact('loans'))->render();

         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
    }

    /**
     * Show this member contribution
     * @param  numeric $memberId 
     * @return view       
     */
    public function contributions(Contribution $contribution,$startDate=null,$endDate=null,$excel=0,$adhersionId)
    {
    	// Abort if member is tricking the system
    	$this->user->abortIfMemberTricksSystem($adhersionId);
    	
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.contributions')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing contributions report');
    
    	$contributions = $contribution->with('member')->betweenDates($startDate,$endDate)->where('adhersion_id',$adhersionId)->get();
    	$total_savings = $contribution->isSaving()->where('adhersion_id',$adhersionId)->sum('amount');
    	$total_withdrawal = $contribution->isWithdrawal()->where('adhersion_id',$adhersionId)->sum('amount');

    	$report = view('reports.member.contributions',compact('contributions','total_savings','total_withdrawal'))->render();
    	if ($excel==1) {
			 toExcel($report,'contributions-report');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


    /**
     * Show loan per status
     * @param      $loan      
     * @param    $startDate 
     * @param    $endDate   
     * @param    $status    
     * @param   $excel     
     * @return    view
     */
    public function loans(Loan $loan,$startDate=null,$endDate=null,$status='all',$excel=0)
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.loans.status')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$results = $loan->with('member')->betweenDates($startDate,$endDate);
    	// if status is not all, then fetch the status
    	if (strtolower($status) !== 'all') {
	    	$results = $results->ofStatus($status);
    	}

    	$loans = $results->orderBy('operation_type','ASC')->get();

    	$report = view('reports.loans.loans',compact('loans'))->render();

    	if ($excel==1) {
			 toExcel($report,$status.'_between_'.request()->segment(3).'_and_'.request()->segment(4));
		}
		if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

    /**
     * Change Contribution that have been changing  
     * @param    $startDate 
     * @param    $endDate   
     * @param    $excel     
     * @return    view
     */
    public function contributionChanges($startDate=null,$endDate=null,$excel=0)
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.savings.changes')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$contributions = Contribution::contributionChanges($startDate,$endDate);
    	$report = view('reports.savings.saving_changes',compact('contributions'))->render();

    	if ($excel==1) {
			 toExcel($report,'Contribution_changes_between_'.request()->segment(3).'_and_'.request()->segment(4));
		}

		if (request()->has('pdf')) {
         	return  htmlToPdf($report);
        }

    	return view('layouts.printing', compact('report'));
    }

    /**
     * Change Contribution that have been changing  
     * @param    $startDate 
     * @param    $endDate   
     * @param    $excel     
     * @return    view
     */
    public function modifiedRefunds($startDate=null,$endDate=null,$excel=0)
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.modified.refunds')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$members = Refund::modifiedRefund($startDate,$endDate);
    	

    	$report = view('reports.refunds.changes',compact('members'))->render();

    	if ($excel==1) {
			 toExcel($report,'Contribution_changes_between_'.request()->segment(4).'_and_'.request()->segment(5));
		}
		if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

    /**
     * Show member with loan per institutions
     * @param  Institution $institution   [description]
     * @param  [type]      $institutionId [description]
     * @return [type]                     [description]
     */
    public function monthlyRefund(Institution $institution,Loan $loan,$institutionId,$excel = 0)
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.monthly.refound')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	// Get the institution by its id
		$institution = $institution->findOrFail($institutionId);

		$institution = $institution->name;
		$members = new Collection($loan->memberWithLoans($institutionId));

		$report = view('reports.member.memberswithloan',compact('members','institution'))->render();
		if ($excel==1) {
			 toExcel($report,$institution.'_monthly_refund_between_'.request()->segment(3).'_and_'.request()->segment(4));
		}
         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


    /**
     * Show member with contribution per institutions
     * @param  Institution $institution   [description]
     * @param  [type]      $institutionId [description]
     * @return [type]                     [description]
     */
    public function monthlyContribution(Institution $institution,User $user,$institutionId,$excel = 0)
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.monthly.refound')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	// Get the institution by its id
		$institution = $institution->findOrFail($institutionId);

		$institution = $institution->name;
		$members = new Collection($user->memberContribute($institutionId));

		$report = view('reports.member.member_monthly_contribute',compact('members','institution'))->render();
		if ($excel==1) {
			 toExcel($report,'monthly saving');
		}
         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

    /**
     * Show member savings levels
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function savingsLevel(Contribution $contribution,$status=null,$month=null,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
         
    	$members = $contribution->savingLevel($status = [$status],$month);


    	$members = new Collection($members);
		$report = view('reports.member.memberssavings',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'savings level');
		}

        if (Input::has('pdf')) {
         	return  htmlToPdf($report);
        }
        
    	return view('layouts.printing', compact('report'));
    }



    /**
     * Show member savings levels all  status
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
 
    public function savingsLevelMember(Contribution $contribution,$month=null,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
    
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$members = $contribution->savingLevel($status = ['actif','inactif','left'],$month); // Consider only these status and ignore left

    	$members = new Collection($members);
		$report  = view('reports.member.memberssavingsmember',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'All members');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));

    }


     /**
     * Show member  from dashboard  without  enddate
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */


    
    public function savingsLeveldashboard(Contribution $contribution,$status=null,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
         
    	$members = $contribution->savingLeveldashboard($status = [$status]);


    	$members = new Collection($members);
		$report = view('reports.member.memberssavingsdashboard',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'savings level');
		}

        if (Input::has('pdf')) {
         	return  htmlToPdf($report);
        }
        
    	return view('layouts.printing', compact('report'));
    }





    /** dashbord  ordinary loan  report  for  current year  **/



    public function dashbordordinary(loan $loan,$status=null,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
         
    	$members = $loan->getLoanOfYear();


    	$members = new Collection($members);
		$report = view('reports.member.dashordinarycurrentyear',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'savings level');
		}

        if (Input::has('pdf')) {
         	return  htmlToPdf($report);
        }
        
    	return view('layouts.printing', compact('report'));
    }


    
    /** dashbord  emergency loan  report  for  current year  **/



    public function dashbordemergency(loan $loan,$status=null,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
         
    	$members = $loan->getEmergencyOfYear();


    	$members = new Collection($members);
		$report = view('reports.member.dashemergencycurrentyear',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'savings level');
		}

        if (Input::has('pdf')) {
         	return  htmlToPdf($report);
        }
        
    	return view('layouts.printing', compact('report'));
    }


   public function savingsLevelMemberdashboard(Contribution $contribution,$institution=null,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
    
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$members = $contribution->savingLeveldashboard($status = ['actif','inactif','left']); // Consider only these status and ignore left

    	$members = new Collection($members);
		$report  = view('reports.member.memberssavingsdashboard',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'All members');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


    /**
     * Show left member with  details
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function savingsLeftMember(Contribution $contribution,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
    
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$members = $contribution->savingleftMember();
    	$members = new Collection($members);
		$report = view('reports.member.leftmember',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'Left members');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


    /**
     * Show new member with  details
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function savingsnewmember(Contribution $contribution,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
   
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$members = $contribution->newMember();
    	$members = new Collection($members);
		$report = view('reports.member.newmember',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'Left members');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


/**
     * Show yer interest report
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function savingsyearinterest(Contribution $contribution,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
   
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$members = $contribution->IndividualyYeatInterest();
    	$members = new Collection($members);
		$report = view('reports.member.yearinterest',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'new members');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


    /**
     * Show institutions list
     * @param  instititions $institution
     * @param        $excel        
     * @return                      
     */
    public function institutionList(Contribution $institution,$excel = 0 )
    {
    	 // First check if the user has the permission to do this
    
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }


    	$members = $institution->getinstitutionlist();
    	$members = new Collection($members);
		$report = view('reports.member.list_institution',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'Left members');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


    


     /* Show member all members with  details
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */

    public function savingsAllDetails(Contribution $contribution,$institition=null,$excel = 0 )
     {
    	 // First check if the user has the permission to do this
    
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$members = $contribution->savingAllMember();
    	$members = new Collection($members);
    	$report = view('reports.member.membersalldetails',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'Members with all details');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }



   
    /**
     * Show member all guarantorss
     * @param  MemberLoanCautionneur $cautionneur 
     * @param        $excel        
     * @return                      
     */
 
    public function allguarantors(MemberLoanCautionneur $cautionneur,$excel = 0 )
    {
         // First check if the user has the permission to do this
    
        if (!$this->user->hasAccess('reports.savings.level')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        $members = $cautionneur->getallguarantors(); // all guarantors  in the table
        
        $report  = view('reports.member.loan_guarantors',compact('members'))->render();
        if ($excel==1) {
             toExcel($report,'All guarantors');
}         if (Input::has('pdf')) {
            return  htmlToPdf($report);
         }
        return view('layouts.printing', compact('report'));

    }







    /**
     * Get loan balance
     * @param  Loan    $loan  [description]
     * @param  integer $excel [description]
     * @return [type]         [description]
     */
    public function loansBalance(Loan $loan,$endDate=null,$excel = 0 )
    {
       
    	if (!$this->user->hasAccess('reports.loan.balance')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$members = $loan->getMembersLoanBalance($endDate);
          
		$report = view('reports.member.memberloans',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'Loan levels');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }


     /**
     * Get loan balance via dashbord
     * @param  Loan    $loan  [description]
     * @param  integer $excel [description]
     * @return [type]         [description]
     */
    public function loansBalancedashbord(Loan $loan,$excel = 0 )
    {
       
    	if (!$this->user->hasAccess('reports.loan.balance')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$members = $loan->getMembersLoanBalancedashbord();
          
		$report = view('reports.member.memberloansdashbord',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'Loan levels');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }
    /**
     * Showing member who are not contributing in x times
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function notContribuing(Contribution $contribution,$institition=null,$excel = 0)
    {
    	 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.savings.irreguralities')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
		$members = $contribution->notContributedIn();
		$members = new Collection($members);
		$report =  view('reports.member.members_not_contributed',compact('members'))->render();
		if ($excel==1) {
			 toExcel($report,'contribution irreguralities');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

    /**
     * Showing member who are not contributing in x times
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function refundIrregularities(Refund $refund,$month=null,$excel = 0)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.refunds.irreguralities')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

		$members = $refund->refundIrregularities($month);
		$members = new Collection($members);
		$report  =  view('reports.member.refund_irregularities',compact('members'))->render();

		if ($excel==1) {
			 toExcel($report,'refund irreguralities');
		}
		
		if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }
    /**
     * Show people who has loan and remains with the 
     * latest installment		
     * @param  Contribution $contribution 
     * @param        $excel        
     * @return                      
     */
    public function refundLatestPayment(Refund $refund,$institition=null,$excel = 0){
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.refunds.refund_latest_payment')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

		$refunds = Refund::refundLatestPayment();
		$report =  view('reports.refunds.refund_latest_payment',compact('refunds'))->render();

		if ($excel==1) {
			 toExcel($report,'Dernier tranche de remboursement credit');
		}

		if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }
    /**
     * Get member who cautioned me
     * @param  MemberLoanCautionneur $cautions    
     * @param                  $startDate   
     * @param   			   $endDate     
     * @param                  $excel       
     * @param                  $adhersionId 
     * @return                              
     */
    public function cautionedMe(MemberLoanCautionneur $cautions,$startDate=null,$endDate=null,$excel=0,$adhersionId)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.members_who_cautionned_me')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$cautions = $cautions->betweenDates($startDate,$endDate)->byAdhersion($adhersionId)->get();
    	$title    = trans('report.members_who_cautionned_me');
    	$member   = ($cautions->isEmpty() == false) ? $cautions->first()->member : null;
    	$type     = 'me';
    	$report =  view('reports.member.cautionned_me',compact('cautions','title','member','type'))->render();
		if ($excel==1) {
			 toExcel($report,'members who cautioned me');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }
      /**
     * Get member who cautioned me
     * @param  MemberLoanCautionneur $cautions    
     * @param                  $startDate   
     * @param   			   $endDate     
     * @param                  $excel       
     * @param                  $adhersionId 
     * @return                              
     */
    public function cautionedByMe(MemberLoanCautionneur $cautions,$startDate=null,$endDate=null,$excel=0,$adhersionId)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.members_cautionned_by_me')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$cautions = $cautions->betweenDates($startDate,$endDate)->byCautionneurAdhersion($adhersionId)->get();
    	$title    = trans('report.members_cautionned_by_me');
    	$member   = ($cautions->isEmpty() == false) ? $cautions->first()->member : null;
    	$type     = 'by_me';
    	$report =  view('reports.member.cautionned_me',compact('cautions','title','member','type'))->render();
		if ($excel==1) {
			 toExcel($report,'members who cautioned me');
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }
    /**
     * Piece Disbursed Saving Report 
     * @param  string $value 
     * @return [type]        
     */
    public function pieceDisbursedSaving(Contribution $contribution,User $user,$transactionid,$excel=0)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.piece.debourse.saving')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$contribution = $contribution->with(['postings','institution'])->byTransaction($transactionid)->first();

		$postings				= [];

		$labels = $this->labels;
    	if (isset($contribution->postings)) 
    	{
	    	$postings = $contribution->postings;
	    	$posting = $postings->first();
	    	$this->labels->title 					= trans('report.piece_debourse_saving_number',['number'=>$posting->transactionid]);
			$this->labels->top_left_upper           = trans('report.done_by');
			$this->labels->top_left_upper_value		= $posting->user->names;
			$this->labels->top_left_under_value		= $posting->created_at->format('Y-m-d');
			$this->labels->top_right_upper_value	= $contribution->adhersion_id;
			$this->labels->top_right_under_value	= $contribution->cheque_number;
    	}
        
    	$report =  view('reports.postings.piece_debourse',compact('postings','user','labels'))->render();

    	if ($excel==1) {
			 toExcel($report,'piece_debourse_saving');
		}
     if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

    /**
     * Piece Disbursed Refund Report 
     * @param  string $value 
     * @return [type]        
     */
    public function pieceDisbursedRefund(Refund $refund,User $user,$transactionid,$excel=0)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.piece.debourse.refund')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

    	$refund = $refund->with('postings')->where('transaction_id',$transactionid)->first();

		$postings				= [];

		$labels = $this->labels;
    	if (isset($refund->postings)) 
    	{
	    	$postings = $refund->postings;
	    	$posting = $postings->first();
	    	$this->labels->title 					= trans('report.piece_debourse_refund_number',['number'=>$posting->transactionid]);
			$this->labels->top_left_upper_value		= $posting->created_at->format('Y-m-d');
			$this->labels->top_left_under_value		= $posting->user->names;
			$this->labels->top_right_upper_value	= $refund->adhersion_id;

			if (!empty($refund)) {
			  $this->labels->top_right_upper_value	= trans('general.many_members');
			}
			$this->labels->top_right_under_value	= $refund->cheque_number;
    	}
        
    	$report =  view('reports.postings.piece_debourse',compact('postings','user','labels'))->render();

    	if ($excel==1) {
			 toExcel($report,'piece_debourse_refund');
		}
     if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

     /**
     * Piece Disbursed Account Report 
     * @param  string $value 
     * @return [type]        
     */
    public function pieceDisbursedAccount(Posting $posting,User $user,$startDate,$endDate,$account,$excel=0)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.piece.debourse.account')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
        
    	$postings = $posting->with(['account','user'])->betweenDates($startDate,$endDate)->forAccount($account)->get();

		$this->labels->top_right_upper			= trans('loan.operator');
		$this->labels->top_right_upper_value	= $this->user->first_name.' '.$this->user->last_name;

    	if (!$postings->isEmpty()) {
   		$posting = $postings->first();
    	$this->labels->title 					= trans('report.piece_debourse_accounting',['number'=>$posting->transactionid]);
		$this->labels->top_left_under_value		= $posting->user->names;
		$this->labels->top_right_under_value	= $posting->cheque_number;
	
    	}
		$labels = $this->labels;
    	$report =  view('reports.postings.piece_debourse',compact('postings','user','labels'))->render();

    	if ($excel==1) {
			 toExcel($report,'pierce_debource_comptable_between_'.request()->segment(3).'_and_'.request()->segment(4));
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

     /**
     * Piece Disbursed Loan Report 
     * @param  string $value 
     * @return [type]        
     */
    public function pieceDisbursedAccounting(Posting $posting, User $User,$transactionid,$excel=0)
    {
    	    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.piece.debourse.accounting')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	$postings = $posting->with(['account','user'])->ByTransaction($transactionid)->get();

		$this->labels->top_right_upper			= trans('loan.operator');
		$this->labels->top_right_upper_value	= $this->user->first_name.' '.$this->user->last_name;

    	if (!$postings->isEmpty()) {
   		
   		$posting = $postings->first();
    	$this->labels->title 					= trans('report.piece_debourse_accounting',['number'=>$posting->transactionid]);
		$this->labels->top_left_under_value		= date('Y-m-d',strtotime($posting->created_at));
		$this->labels->top_right_under_value	= $posting->cheque_number;
	
    	}
		$labels = $this->labels;
    	$report =  view('reports.postings.piece_debourse',compact('postings','User','labels'))->render();

    	if ($excel==1) {
			 toExcel($report,'pierce_debource_comptable_between_'.request()->segment(3).'_and_'.request()->segment(4));
}         if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }
     /**
     * Piece Disbursed Loan Report 
     * @param  string $value 
     * @return [type]        
     */
    public function pieceDisbursedLoan(Loan $loan,User $User,$transactionid,$excel=0)
    {
    	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.piece.debourse.loan')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    	/** @todo finish pierce debourse */
    	$loan = $loan->with(['postings'])->byTransaction($transactionid)->first();

		$postings				= [];

    	$this->labels->top_left_upper			= trans('loan.bank');
		$this->labels->top_left_under			= trans('report.payment_date');
		$this->labels->top_right_upper			= trans('loan.beneficiaire');
		$this->labels->top_right_under			= trans('loan.cheque_number');

		$labels = $this->labels;

    	if (isset($loan->postings)) 
    	{
    		$transactionid = null;
    		if (!$loan->postings->isEmpty()) {
    			$transactionid = $loan->postings->first()->transactionid;
    		}

			$this->labels->title 					= trans('report.piece_debourse_loan',['number'=>$transactionid]);
	    	$postings = $loan->postings;
			$this->labels->top_left_upper_value		= $loan->bank_id;
			$this->labels->top_left_under_value		= $loan->created_at->format('Y-m-d');
			$this->labels->top_right_upper_value	= $loan->member->names;
			$this->labels->top_right_under_value	= $loan->cheque_number;
    	}
        
    	$report =  view('reports.postings.piece_debourse',compact('postings','User','labels'))->render();

    	if ($excel==1) {
			 toExcel($report,'piece_debourse_pret');
		}
	     if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
    	return view('layouts.printing', compact('report'));
    }

    /**
     * Set labels
     */
    private function setLabels()
    {	
    		$this->labels 							= new stdClass();
	    	$this->labels->title 					= false;

	    	$this->labels->top_left_upper			= trans('loan.bank');
			$this->labels->top_left_under			= trans('report.payment_date');
			$this->labels->top_right_upper			= trans('loan.beneficiaire');
			$this->labels->top_right_under			= trans('loan.cheque_number');

			$this->labels->top_left_upper_value		= trans('general.not_available');
			$this->labels->top_left_under_value		= trans('general.not_available');
			$this->labels->top_right_upper_value	= trans('general.not_available');
			$this->labels->top_right_under_value	= trans('general.not_available');
    }

    /**
     * Report loans that are on risk by x amount of months
     * @param  	 $months 	
     * @return 	 response
     */
    public function loanAtRiskBy($institution,$excel = 0)
    {
    	// $results = Loan::atRisk();
    	// Calling Store procudure to get loans at risk
    	$loans 	 = Loan::atRisk();	
    	$report =  view('reports.loans.at-risk',compact('loans'))->render();

    	if ($excel==1) {
			 toExcel($report,'at-risk');
		}
	     if (Input::has('pdf')) {
         	return  htmlToPdf($report);
         }
		return view('layouts.printing', compact('report'));
    }
}
