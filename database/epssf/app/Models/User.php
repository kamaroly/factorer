<?php
namespace Ceb\Models;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentinelModel;
use Ceb\Models\Contribution;
use Ceb\Models\Loan;
use Ceb\Models\MemberLoanCautionneur;
use Ceb\Models\Setting;
use Ceb\Traits\LogsActivity;
use Exception;
use Fenos\Notifynder\Notifable;
//use Fenos\Notifynder\Traits\Notifable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogsActivityInterface;
use Vinkla\Hashids\Facades\Hashids;
use Sentinel\Models\User as SentinelUser;

class User extends SentinelUser {
	use Notifable;
	//use LogsActivity;
	use SoftDeletes;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
	protected $dates = ['created_at','deleted_at']; //, 'date_of_birth', 'updated_at'
	/**
	 * Wished amount percentage
	 * @var float
	 */
	protected $rightToLoanPercentage = 2.5;
	/**
	 *  Refund fees
	 * @var integer
	 */
	public $refund_fee = 0;

	/**
	 * Keeps latest load id
	 * @var 
	 */
	public $appends = ['loan_id'];
	
	/**
	 * Montly amount the user should refund
	 * @var integer
	 */
	public $loanMonthlyRefundFee = 0;

	/**
	 * Minimum months a user needs to have before having a loan.
	 * @var 
	 */
	protected $minimumMonthsToLoan;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	public $fillable = [
		'adhersion_id',
		'district',
		'province',
		'institution_id',
		'service',
		'termination_date',
		'first_name',
		'last_name',
		'password',
		'date_of_birth',
		'sex',
		'member_nid',
		'nationality',
		'email',
		'telephone',
		'monthly_fee',
		'photo',
		'signature',
		'employee_id',
		'bank_id',
		'bank_account',
		
	];
	/**
	 * The Eloquent group model.
	 *
	 * @var string
	 */
	protected static $groupModel = 'Cartalyst\Sentry\Groups\Eloquent\Group';
	/**
	 * The user groups pivot table name.
	 *
	 * @var string
	 */
	protected static $userGroupsPivot = 'users_groups';
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
	/**
	 * Entry point for our model
	 * @param Setting $setting 
	 */
	function __construct() {
		$this->rightToLoanPercentage = Setting::keyValue('loan.wished.amount');
		$this->minimumMonthsToLoan   = Setting::keyValue('loan.member.minimum.months');
	}
	 /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $keyword)
    {
	 return $query->where('first_name', 'LIKE', '%' . $keyword . '%')
		            ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
		            ->orWhere('email', 'LIKE', '%' . $keyword . '%')
		            ->orWhere('member_nid', 'LIKE', '%' . $keyword . '%')
		            ->orWhere('adhersion_id', 'LIKE', '%' . $keyword . '%');
	}

	/**
	 * Fromat Member NID
	 * @param   $value 
	 * @return  
	 */
	public function getMemberNidAttribute($value)
	{
		return removeSpaces($value);
	}

	/** 
	 * Get member names
	 * @return string 
	 */
	public function names() {
		return $this->first_name . ' ' . $this->last_name;
	}
	/**
	 * Get latest ordinary loan for this member
	 * @return -1 if the user is not eligible for  loan to regulate
	 * @return  1 if the user can only regulate installments
	 * @return  2 if the user can regulate both installments and amount <=>
	 */
	public function getLoanToRegulateAttribute()
	{
		$loan = $this->loans()->isOrdinary()->IsNotUmergency()->orderBy('created_at','DESC')->first();
		// If not have active loan we have nothing to do here
		if ($this->hasActiveLoan() == false || is_null($loan)) {
			return -1;
		}
		// Can regulate echeance
		if ($loan->loan_to_repay >= $loan->right_to_loan) {
			return 1;
		}
		// Can regulate amount
		if ($loan->loan_to_repay < $loan->right_to_loan) {
			return 2;
		}
		return -1;
	}	

	 /** Member bank
	 * @return Ceb\Models\Institution
	 */
	public function bank() {
		return $this->belongsTo('Ceb\Models\Bank');
	}
	
	/**
	 * Member institution
	 * @return Ceb\Models\Institution
	 */
	public function institution() {
		return $this->belongsTo('Ceb\Models\Institution');
	}

	/**
	 * Attornies for this memebr
	 * @return object attorney
	 */
	public function attornies()
	{
		return $this->hasMany('Ceb\Models\Attorney');
	}
	/**
	 * Get the contributions for the Member.
	 */
	public function contributions() {
		return $this->hasMany('Ceb\Models\Contribution', 'adhersion_id', 'adhersion_id');
	}
	/**
	 * Get the monthly fees inventory for the Member.
	 */
	public function monthlyFeeInventories() {
		return $this->hasMany('Ceb\Models\MemberMontlyFeeLog', 'adhersion_id', 'adhersion_id');
	}
	
	/**
	 * Get member loans
	 */
	public function loans() {
		return $this->hasMany('Ceb\Models\Loan', 'adhersion_id', 'adhersion_id');
	}

	/**
	 * Member refunds
	 * @return Objects contains all refunds by this memebr
	 */
	public function refunds() {
		return $this->hasMany('Ceb\Models\Refund', 'adhersion_id', 'adhersion_id');
	}
	/**
	 * Member CAUTIONS
	 * @return Objects contains all people this member cautioned or were their cautionneur
	 */
	public function cautions() {

		return $this->hasMany('Ceb\Models\MemberLoanCautionneur', 'cautionneur_adhresion_id', 'adhersion_id');
	}
    
    /**
	 * Member who cautioned this members
	 * @return Objects contains all people who were cautionneur for this member
	 */
	public function cautioned() {

		return $this->hasMany('Ceb\Models\MemberLoanCautionneur', 'member_adhersion_id', 'adhersion_id');
	}
	/**
	 * Member who cautioned this members for report
	 * @return Objects contains all refunds by this memebr
	 */
	public function getCautionedMeAttribute() {
          
		return $this->cautioned()->active()->get();
	}

		/**
	 * Member who cautioned this members for  dashboard
	 * @return Objects contains all refunds by this memebr
	 */
	public function getCautionedMedashAttribute() {

          
		return $this->cautioned()->activedash()->get();
	}
    /**
     * Get caution amount attributes
     * @return [type] [description]
     */
    public function getCautionAmountAttribute()
    {

    	return $this->cautioned->sum('amount');
    }
    /**
     * Get caution refunded amount attributes
     * @return 
     */
    public function getCautionRefundedAttribute()
    {
    	return $this->cautioned->sum('refunded_amount');
    }
    
    /** Get caution balance */
    public function getCautionBalanceAttribute()
    {
    	return $this->caution_amount - $this->caution_refunded;
    }
    /**
     * Relationship with leaves
     * @return  leave object
     */
    public function leaves()
    {
        return $this->hasMany('Ceb\Models\Leave');
    }
    /**
     * Get total refund attribute
     * @return [type] [description]
     */
    public function getTotalRefundsAttribue()
    {
    	return $this->totalRefunds();
    }
	/**
	 * Get total refunds by this member;
	 * @return numeric
	 */
	public function totalRefunds() {
		return $this->refunds()->sum('amount');
	}
	/**
	 * total_refunds attribute
	 * @return  
	 */
	public function getTotalRefundsAttribute()
	{
		return $this->refunds()->sum('amount');
	}

	/**
	 * Get contribution counts for this Year
	 * @return 
	 */
	public function getCurrentYearContributionsCountAttribute()
	{
		return $this->contributions()->currentYear()->isSavingcount()->count();
	}

	/**
	 * Confirm if this Member can release  guarantors / cautionneur
	 * That are linked to him/her
	 * @return  boolean
	 */
	public function getCanReleaseGuarantorAttribute()
	{
		return ($this->total_contribution >= $this->loan_balance);
	}

	/**
	 * Release cautionneur / Guarantors for this loan 
	 * by reseting the balance for this loan
	 * @return  
	 */
	public function releaseGuarantors()
	{
		// 1. Get all cautionneur for this members that are 
		//    approved so that we can avoid to release
		//    guarantors for loans in processes
		$loanIds = $this->loans()->approved()
					  ->whereIn('id', $this->cautioned()->get()->lists('loan_id'))
					  ->lists('id')->toArray();

		// 2. Update approved loans only 
		return dd($this->cautioned()->whereIn('id', $loanIds)->count());
	}

	/**
	 * Get the total amount of contribution
	 */
	public function totalContributions() {
		$saving     = $this->contributions()->isSaving()->sum('amount');
		$withdrawal = $this->contributions()->isWithdrawal()->sum('amount');
		return (int) $saving - $withdrawal;
	}

	/**
	 * Get the total amount of contribution attributes 
	 */
	public function getTotalContributionAttribute() {
		return $this->totalContributions();
	}
	
	/**
	 * Get the loan balance
	 * @return numeric
	 */
	public function loanBalance() {

		$balance            = 0;
		$totalLoanAmounts   = $this->totalLoans();
		$totalRefundAmounts = $this->totalRefunds();

		// Don't consider emergency loan balance if this member
		// has emergency loan as requested by @olivier
		if ($this->has_active_emergencyLoan) {
			$totalLoanAmounts -= $this->active_emergency_loan->emergency_balance;
		}

		// Show even negative number
		return ($balance = $totalLoanAmounts - $totalRefundAmounts);
	}
	/**
	 * Check if this person has Ceb minimum loan months
	 * @return 
	 */
	public function scopeEligible($query)
	{
		return $query->where('created_at','<=',DB::raw('DATE_SUB(now(), INTERVAL '.env('LOAN_MINIMUM_MONTHS',6).' MONTH)'));
	}

	/**
	 * Get members by institution
	 * @param   $query         
	 * @param   $institutionId 
	 * @return  
	 */
    public function scopeByInstitution($query,$institutionId)
    {
        return $query->where('institution_id',$institutionId);
    }

	/**
	 * loan_balance attribute
	 * @return numeric
	 */
	public function getLoanBalanceAttribute()
	{
		return $this->loanBalance();
	}

	/**
	 * Get Member amount to Bond
	 * @return  
	 */
	public function getAmountToBondAttribute()
	{
		$amountToBond  = 0;
		$loanBalance   = $this->loanBalance();
		$contributions = $this->totalContributions();

		$amountToBond = $contributions - $loanBalance;
		
		return $amountToBond > 0 ? $amountToBond : 0;
	}

	/**
	 * Get remaining payment installment
	 * 
	 * @return int number of remaining installment
	 */
	public function remainingInstallment()
	{
		$installments = 0;
		try
		{
			
			if ($this->has_active_loan) {
				$loan_balance = $this->loanBalance();
				
				// Get monthly fee that is supposed to be paid by this member
				$monthly_fee  = $this->loanMonthlyFees();//$this->latestLoan()->monthly_fees;
				// No active loan therefore remaining installment is 0
				$installments = $loan_balance / $monthly_fee;
				return round($installments);
			}
			// Add emergency loan if we have it
			// if ($this->has_active_emergency_loan) {
			// 	$emergency_loan = $this->active_emergency_loan;
			// 	$emergency_loan_refund = $emergency_loan->emergency_refund;
			// 	$emergency_balance     = $emergency_loan->emergency_balance;
			// 	$emergencyLoanAmount = $emergency_balance + $emergency_loan_refund;
			// 	$emergency_monthly_fee = $emergency_loan->monthly_fees;
			// 	$emergency_installments = $emergency_balance / $emergency_monthly_fee;
			// 	// Remove emergency loan since we have added it in total loans
			// 	// and to recalculations for the installments
			// 	$loan_balance -= ($emergency_balance);
			// 	// $monthly_fee  -=$emergency_monthly_fee;
			// 	// If we have already paid for this loan, then don't count 
			// 	// the refund for the emergency loan, so that we can be 
			// 	// more accurate on the installments 
				
			// 	// Add the emergency loan balance for us to be able to balance
			// 	// the installments
			// 	$installments = $loan_balance / $monthly_fee;
				
			// 	// Add the difference of emergency loan installments 
			// 	// if installments are < than emergency loan installments
			// 	if ($installments < $emergency_installments) {
			// 		$installments += abs($installments - $emergency_installments);
			// 	}
			// }
		}
		catch(Exception $e)
		{
			Log::critical($e->getMessage());
		}
		
		return intval(floor($installments));
	}
	/**
	 * Get current active cautions
	 * @return [type] [description]
	 */
	public function getCurrentCautionsAttribute()
	{
		return $this->cautions()->where('amount', '>', DB::raw('refunded_amount'))->get();
	}

	/**
	 * Determine if this member can be added
	 * as cautionneur to a loan, default 
	 * maximum cautions are 3
	 * @return boolean true, if eligiable and false if not
	 */
	public function canCaution($adhersionID)
	{
		$cautions = $this->cautions()->active()->get();
		// This member is eligible to caution when he has less than allowed number of cautionneur 
		// or when He has cautionned this adhersion ID before
		// 1. If this member was cautionned before then accept this
		if ( ! $cautions->where('member_adhersion_id',$adhersionID)->isEmpty()) {
			return TRUE;
		}

		// This cautionneur is new, check if he hasn't maximize the number of allowed
		// people to caution
		return $this->cautions()
					->active() // Get active cautions
					->get()->unique('member_adhersion_id') // Distinct member adhersion
					->count() < env('MAX_CAUTIONS',3);
	}

	/**
	 * Get the remaining tranches for this member
	 * @return  number
	 */
	public function getRemainingTranchesAttribute()
	{
		return $this->remainingInstallment();
	}
	/**
	 * Determine if this member has active Loan
	 *
	 * @return  bool
	 */
	public function hasActiveLoan() {
		return $this->loanBalance() > 0;
	}
    
	/**
	 * Check if a user has active loan
	 * @return  bool
	 */
	public function getHasActiveLoanAttribute()
	{
		return ($this->totalLoans() - $this->totalRefunds())  > 0;
	}

	/**
	 * Get active loans for this members
	 * @return  
	 */
	public function getActiveLoansAttribute()
	{
		return $this->loans()->active()->get();
	}

	/**
	 * Get Right to loan
	 */
	public function generalRightToLoan() 
	{
		// If the user has active loan then consider 
		// Latest ordinary loan right to loan 
		// as his /her current right to loan
		if ($this->hasActiveLoan()) {
		    $loan = $this->loans()->isOrdinary()->IsNotUmergency()->orderBy('created_at','DESC')->first();
		    
		    // If we found ordinary loan then 
		    // use it as right to loan
			
		    if($loan){
		     return $loan->right_to_loan - $loan->loan_to_repay;
		    }
		}
		return $this->total_contribution * $this->rightToLoanPercentage;
	}
	/**
	 * general_right_to_loan attributes
	 * 		
	 * @return number
	 */
	public function getGeneralRightToLoanAttribute()
	{
		return $this->generalRightToLoan();
	}
	/**
	 * Right to loan considering loan
	 * @param  string $value
	 * @return 
	 */
	public function rightToLoan()
	{
		// If this member has active loan, as we calculate 
		// Amount to loan that he is eligeable for we 
		// need to consider right to loan a member
		// had before we give him this loan
		$latestLoan = $this->latestLoan();
		$contributions 	= $this->contributions();
		if ($this->loan_to_regulate !==-1 and strpos($latestLoan->operation_type,'ordinary_loan') !== false) {
			return $latestLoan->right_to_loan - $latestLoan->loan_to_repay;
		}
		
		// Since this member has active loan, let's determine
		// what is his right loan as of previous loan
		// Then deduct the loan he was given
		return  $this->totalContributions() * $this->rightToLoanPercentage;
	}
	/**
	 * right_to_loan_attribute
	 * @return 
	 */
	public function getRightToLoanAttribute()
	{
		return $this->generalRightToLoan();
	}
    
    public function getLatestOrdinaryLoanAttribute()
    {
    	return $this->loans()->isOrdinary()->isNotUmergency()->orderBy('id','DESC')->first();
    }
	/**
	 * Determine if this member still have right to loan
	 * 
	 * @return boolean 
	 */
	public function getHasMoreRightToLoanAttribute()
	{
		$loan = $this->latest_ordinary_loan;
		
		if (is_null($loan)) {
			return false;
		}
		return $loan->loan_to_repay < $loan->right_to_loan;
	}
	/**
	 * Get remaining amount right to loan attribute
	 * @return numeric
	 */
	public function getRemainingRightToLoanAttribute()
	{
	  	// Get latest ordinary loan
	  	$loan = $this->latest_ordinary_loan;
	  	return $loan->right_to_loan - $loan->right_to_loan;
	}
	/**
	 * Get total loan attribute
	 * @return numeric
	 */
	public function getTotalLoanAttribute()
	{
		return $this->totalLoans();
	}
	/**
	 * total Loan that user has taken
	 * @return  numeric
	 */
	public function totalLoans() {
		return $this->loans()->approved()->sum('loan_to_repay');
	}
	public function loanSumRelation()
	{
	    return $this->hasMany('Ceb\Models\Loan', 'adhersion_id', 'adhersion_id')->selectRaw('adhersion_id, sum(loan_to_repay) as loan_to_repay')
	    	->where('status','approved')
	        ->groupBy('adhersion_id');
	}
	public function getLoanSumAttribute()
	{
		$sumLoan = $this->loanSumRelation;
	    return $sumLoan->isEmpty() ? 0:
	        intval($sumLoan->first()->loan_to_repay) ;
	}
	public function refundSumRelation()
	{
	    return $this->hasMany('Ceb\Models\Refund', 'adhersion_id', 'adhersion_id')->selectRaw('adhersion_id, sum(amount) as refund')
	        ->groupBy('adhersion_id');
	}
	public function getRefundSumAttribute()
	{
		$sumRefund = $this->refundSumRelation;
	    return $sumRefund->isEmpty() ? 0:
	        intval($sumRefund->first()->refund) ;
	}
	/**
	 * Determine if this user has active emergency Loan
	 * @return boolean 
	 */
	public function getHasActiveEmergencyLoanAttribute()
	{
		// IF this collection is not empty then we have loans
		return 	$this->loans()->isUmergency()->loanWithRelicat()->isEmpty() === FALSE;
	}

	/**
	 * Get active emergency loan
	 * @return 
	 */
     
    public function getActiveEmergencyLoanAttribute()
	{
		$activeEmergencyLoand = $this->loans()->isUmergency()->loanWithRelicat()->first();
		// Buld the entire object for this loan by adding other necessary attributes
		// so that i can be considered by the data.
		if ($activeEmergencyLoand == null)
		{
			return $this->loans()->isNotPaidUmergency()->orderBy('id','desc')->first();
		}

		//dd($activeEmergencyLoand);
		$loan                    = $this->loans()->where('loan_contract',$activeEmergencyLoand->loan_contract)
												 ->orderBy('id','desc')->first();

		if ($activeEmergencyLoand->emergency_balance <> $loan->emergency_balance) {
				$loan->emergency_refund  = $activeEmergencyLoand->monthly_fees;
				$loan->emergency_balance = $activeEmergencyLoand->emergency_balance;
		        $loan->save();
		}
	
		return $loan;
	}



	/**
     * Get emergency loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getEmergencyMonthlyFeeAttribute()
    {
        /** Make sure this is a valid emergency loan before proceeding */
        $monthly_fee = 0;
        try
        {
        	if($this->has_active_emergency_loan)
        	{
        		$monthly_fee =  $this->active_emergency_loan->monthly_fees;
        	}
        	
    	}
        catch(Exception $e){
        	Log::critical($e->getMessage());
        } 
        return $monthly_fee;
    }
	
	/**
	 * Get Emergency Loan with Relicat
	 * @return 
	 */
	public function getEmergencyWithRelicatAttribute()
	{
		return $this->loans()->isUmergency()->loanWithRelicat();
	}

	/**
	 * Get member loan monthly fees that
	 * He is supposed to pay
	 * @return numeric with the fees this member need to pay
	 */
	public function loanMonthlyFees() {
		    $monthly_fee = 0;
			if ($this->has_active_loan && !is_null($latest = $this->latestLoan())) {
				$monthly_fee = $latest->monthly_fees;
			}

			// If monthly fee is 0 and active emergency loan is avaiable, this member
			// must be having only emergency loan and that emergency loand might 
			// be his first loan, let's treat this special case by considering
			// Emergency loan monthly fee when the above is the case
		 	if (($monthly_fee === 0) && $this->has_active_emergency_loan) {

		 		// Consider monthly fees of emergency if it is available
	 			$monthly_fee = $this->active_emergency_loan->monthly_fees;

			 	// If this one is an emergency loan, then reset 
			 	// monthly loan to zero. When latest loan is
			 	// Empty, Member has only emergency loan.
			 	if ( ! empty($latest) && ($this->active_emergency_loan->id == $latest->id)){
					$monthly_fee = 0;
				}
			}

			try {
				// If this balance is the same as Emergency Loan balance, then 
				// Reset this to 0, since only emergency loan is remaining
				$emergencyLoan     = $this->emergency_with_relicat->first();
				$emergencyBalance  = $emergencyLoan->loan_to_repay - $emergencyLoan->refund_amount;
				$activeLoanBalance = $this->totalLoans() - $this->totalRefunds();

				if ($activeLoanBalance === $emergencyBalance) {
					$monthly_fee = 0;
				}
			} catch (\Exception $e) {
				// Catch error that might have occurend in above codes 
				Log::error($e);
			}
		return $monthly_fee;	
	}

	/**
	 * Get loan monthly fee
	 * @return number
	 */
	public function getLoanMonthlyFeeAttribute()
	{
		return $this->loanMonthlyFees();
	}
	
	/**
	 * Get latest Loan that this member has gotten
	 * @return user Object
	 */
	public function latestLoan() {
		return $this->loans()->isNotUmergency()->isNotReliquant()->approved()->orderBy('id', 'desc')->first();
	}

	/**
	 * Latest Loan Id
	 * @return  
	 */
	public function getLoanIdAttribute()
	{
		$loanId   = false;

		if ($loan = $this->latestLoan()) {
			$loanId = $loan->id;
		}
		// If we don't have ordinary loan, then check if this member 
		// has emergency loan, then consider it.
		if (empty($loan) && $this->has_active_emergency_loan) {
			$loanId = $this->active_emergency_loan->id;
		}

		return $loanId;
	}
	/**
	 * Get latest Loan that this member has gotten
	 * @return user Object
	 */
	public function latestLoanWithEmergency() {
		return $this->loans()->isNotReliquant()->approved()->orderBy('id', 'desc')->first();
	}	
	/**
	 * Get latest loan attribute
	 * @return  
	 */
	public function getLatestLoanAttribute()
	{
		return $this->latestLoan();
	}

	/**
	 * Find a member by his adhersion ID
	 * @param  integer $adhersionId member adhersion number
	 * @return this
	 */
	public function findByAdhersion($adhersionId) {
		return $this->where('adhersion_id', $adhersionId)->first();
	}
	/**
     * A sure method to generate a unique adhersionId 
     *
     * @return string
     */
    public function generateAdhersionID()
    {
    	$max = self::where('email','<>','admin@admin.com')->max('adhersion_id');
	    $max = substr($max, 4);
        do {
            $max++;
			$newAdhersionNumber = '20210'.($max);
        } // Already in the DB? Fail. Try again
        while (self::adhersionIdExists($newAdhersionNumber));
        return $newAdhersionNumber;
    }
	 /**
     * Checks whether a adhersionid exists in the database or not
     *
     * @param $key
     * @return bool
     */
    private function adhersionIdExists($adhersionId)
    {
        $adhersionId = self::where('adhersion_id', '=', $adhersionId)->limit(1)->count();
        if ($adhersionId > 0) return true;
        return false;
    }
	/**
	 * Find member by adhresion
	 * @param   $query        
	 * @param   $adhersion_id 
	 * @return query builder
	 */
	public function scopeByAdhersion($query,$adhersion_id)
	{
		return $query->where('adhersion_id',$adhersion_id);		
	}

	/**
	 * Exclude left members 
	 * @param   $query 
	 * @return  queryBuilder       
	 */
	public function scopeNotLeft($query)
	{
		return $query->where('status','<>','left');
	}
	/**
	 * Get member who has left
	 * @param  $query 
	 * @return  
	 */
	public function scopeHasLeft($query)
	{
		return $query->where('status','=','left');
		            
	}
	/**
	 * Get member who has inactive
	 * @param  $query 
	 * @return  
	 */
	public function scopeIsInactive($query)
	{
		return $query->where('status','=','inactif');
		            
	}
	/**
	 * Get member who are active
	 * @param  $query 
	 * @return 
	 */
	public function scopeIsActive($query)
	{
		return $query->where('status','=','Actif');


	}




	/**
	 * Get member details
	 * @param  $query 
	 * @return  
	 */
		 public function getmemberinitiator($user_id)
         {
		 if (!empty($user_id)) {


      $idd = " id = ".$user_id;
         }


    	$query = "SELECT first_name ,last_name  FROM users  
    	                       WHERE $idd ;";
    	return DB::select($query);
    }


	/**
	 * Get member who has left
	 * @param  $query 
	 * @return  
	 */
		 public function memberContribute($institition = '')
    {
    	// if user passed institution then consider it in the select
    	if (!empty($institition)) {

    		$institition = " and institution_id = ".$institition;
    	}

    	$query = "SELECT a.*,b.* 

    	                       FROM users as a left join institutions as b on a.institution_id =b.id 
    	                       WHERE adhersion_id<>'200799999' and  status <>'left'  $institition ;";
    	return DB::select($query);
    }

	/**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst($value);
    }
    /**
     * Set the user's last name.
     *
     * @param  string  $value
     * @return string
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst($value);
    } 
    
    /**
     * Get the user's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function getEmployeeIdAttribute($value)
    {
        return is_null($value) ? trans('general.not_available') :  ucfirst($value);
    }
    /**
     * Get name attributes
     * @return  string
     */
    public function getNamesAttribute()
    {
    	return $this->first_name .' '.$this->last_name;
    }
    /**
     * Get institution attribute
     * @return  
     */
    public function getInstitutionNameAttribute()
    {
    	return $this->institution->name;
    }
    /**
     * Use a mutator to derive the appropriate hash for this user
     *
     * @return mixed
     */
    public function getHashAttribute()
    {
        return Hashids::encode($this->id);
    }
    /**
	 * See if the user is in the given group.
	 *
	 * @param \Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 */
	public function inGroup(GroupInterface $group)
	{
		foreach ($this->getGroups() as $_group)
		{
			if ($_group->getId() == $group->getId())
			{
				return true;
			}
		}
		return false;
	}
		/**
	 * Returns an array of groups which the given
	 * user belongs to.
	 *
	 * @return array
	 */
	public function getGroups()
	{
		if ( ! $this->userGroups)
		{
			$this->userGroups = $this->groups()->get();
		}
		return $this->userGroups;
	}
	/**
	 * Returns the relationship between users and groups.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function groups()
	{
		return $this->belongsToMany(static::$groupModel, static::$userGroupsPivot);
	}

	/**
	 * Check if a user is admin
	 * @return boolean true if he is admin
	 */
	public function isAdmin()
	{
		return $this->hasAccess('admin');
	}

	/**
	 * Check the user can vciew his own profile
	 * @return  
	 */
	public function canViewOwnProfile()
	{
		return $this->hasAccess('ceb.view.own.profile');
	}

	/**
	 * Check if current user is a normal member
	 * @return boolean 
	 */
	public function isNormalMember()
	{
		return ($this->canViewOwnProfile() && !$this->isAdmin());
	}

	/**
	 * Check if this user is a ceb member
	 * @return boolean 
	 */
	public function isMember()
	{
		return $this->hasAccess('ceb.member');
	}

	/**
	 * See if a group has access to the passed permission(s).
	 *
	 * If multiple permissions are passed, the group must
	 * have access to all permissions passed through, unless the
	 * "all" flag is set to false.
	 *
	 * @param  string|array  $permissions
	 * @param  bool  $all
	 * @return bool
	 */
	public function scopeHasRight($query,$permissions)
	{
		// If this user has the right, then pass the query otherwise
		// Fail the query intentionally 	
		
		if ($this->hasAccess($permissions) == true) {
			return $query->where(DB::raw('1=1'));
		}
		// Fail this query scope because this person does not have the right
	    return $query->where(DB::raw('1=2'));
	}

	/**
	 * Validates the user and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @return bool
	 * @throws \Cartalyst\Sentry\Users\LoginRequiredException
	 * @throws \Cartalyst\Sentry\Users\PasswordRequiredException
	 * @throws \Cartalyst\Sentry\Users\UserExistsException
	 */
	public function validate()
	{
		if ( ! $login = $this->getLoginName() )
		{
			throw new LoginRequiredException("A login is required for a user, none given.");
		}
		if ( ! $password = $this->getPasswordName())
		{
			throw new PasswordRequiredException("A password is required for user [$login], none given.");
		}
		// Check if the user already exists
		$query = $this->newQuery();
		$persistedUser = $query->where($this->getLoginName(), '=', $login)->first();
		if ($persistedUser and $persistedUser->getId() != $this->getId())
		{
			throw new UserExistsException("A user already exists with login [$login], logins must be unique for users.");
		}
		return true;
	}

	/**
	 * Abort if member is trying to trick system by 
	 * changing adhersion in the URL
	 * @param   $adhersionId 
	 * @return  
	 */
	public function abortIfMemberTricksSystem($adhersionId)
	{
		// Don't allow member to look for other members data
		// 
		if ($this->isNormalMember() && $this->adhersion_id <> $adhersionId) {
			flash()->error(trans('general.we_cannot_find_what_you_are_looking_for'));

			header('Location: ' .route('home'));
			die();
		}
	}
}