<?php
namespace Ceb\ViewComposers;

use Ceb\Models\Contribution;
use Ceb\Models\Institution;
use Ceb\Models\Loan;
use Ceb\Models\Refund;
use Ceb\Models\User;
use Illuminate\Contracts\View\View;
use Ceb\Repositories\Reports\GraphicReportRepository;
/**
 * AccountViewComposer
 */
class DashboardViewComposer {
	
	public $dashboard  = [];

	function __construct(Institution $institution,Loan $loan,User $member,Refund $refund,Contribution $contribution) {

		$this->dashboard['ordinary_loan']			= $loan->ofStatus('approved')->IsNotUmergency('loan')->IsNotRelicat('remainers')->ofCreated('2021')->sum('loan_to_repay');
		$this->dashboard['social_loan']				= $loan->ofStatus('approved')->IsUmergency('emergency')->ofCreated('2021')->sum('loan_to_repay');
		$this->dashboard['special_loan']			= $loan->ofStatus('approved')->ofType('special_loan')->sum('loan_to_repay');
		$this->dashboard['urgent_ordinary_loan']	= $loan->ofStatus('approved')->ofType('urgent_ordinary_loan')->count();
		$this->dashboard['refunded_amount']			= $loan->sumPartSocial();
		$this->dashboard['outstanding_loan']		= $loan->sumOutStanding();
		$this->dashboard['left_members_count']		= $member->hasLeft()->count();
		$this->dashboard['inactive_members_count']	= $member->isInactive()->count();
		$this->dashboard['active_members_count']	= $member->isActive()->count();
		$this->dashboard['institutions']			= $institution->count();
		$this->dashboard['outstandingLoans']		= $loan->countOutStanding();
		$this->dashboard['paidLoans']				= $loan->countPaid();			
		$this->dashboard['savings_level']			= $contribution->isSaving()->sum('amount') - $contribution->isWithdrawal()->sum('amount');
	}


/**public function index(GraphicReportRepository $report)
	{
	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('reports.index')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing reports charts index');
		
		$contributions    = $report->getMonthlyContribution();
	
		$institutions     = $report->getCountMemberPerInstitution();
		
		return view('reports.index',compact('contributions','loans','institutions','institutionsLoan'));
	}**/
      
	public function compose(View $view) {
		$view->with('dashboarddata',$this->dashboard);
	}
}