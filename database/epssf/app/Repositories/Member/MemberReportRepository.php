<?php 
namespace Ceb\Repositories\Member;
use Str;
use Ceb\Models\User;
use Ceb\Models\Loan;
use Ceb\Traits\FileTrait;
use Ceb\Models\Contribution;
use Cartalyst\Sentry\Sentry;
use Ceb\Traits\TransactionTrait;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Ceb\Models\MonthlyFeeInventory;
use Sentinel\DataTransferObjects\BaseResponse;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Sentinel\DataTransferObjects\FailureResponse;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Cartalyst\Sentry\Facades\Laravel\Sentry as AuthenticatedUser;

class MemberReportRepository{

	/**
	 * Get Members with Her Loan than Contribution
	 * @param   $startDate   
	 * @param   $endDate     
	 * @param   $institution 
	 * @return  
	 */
	public static function getHigherLoanThanContribution($institution)
	{
		// 1. Get members with active loans
		$members = User::byInstitution($institution)->get();
		$membersWithActiveLoan = $members->filter(function($member){
									return $member->hasActiveLoan();
								});
		// 2. Filter member with higher loan than contributions
		return $membersWithActiveLoan->filter(function($member){
				return $member->loan_balance > $member->total_contribution;
		});
	}
}
