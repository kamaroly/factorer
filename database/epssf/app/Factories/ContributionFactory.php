<?php
namespace Ceb\Factories;
use Ceb\Models\User;
use Ceb\Models\Institution;
use Ceb\Models\Contribution;
use Ceb\Models\DefaultAccount;
use Ceb\Traits\TransactionTrait;
use Illuminate\Support\Collection as arrayCollection;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;

/**
 * This factory helps Contribution
 */
class ContributionFactory {
	use TransactionTrait;
	function __construct(Session $session, Institution $institution,User $member,Contribution $contribution) {
		$this->session      = $session;
		$this->member       = $member;
		$this->contribution = $contribution;
		$this->institution  = $institution;
	}
	/**
	 * Set members by institutions
	 * @param integer $institutionId
	 */
	public function setByInsitution($institutionId = 1) {

		// Do we have some savings ongoing ?
		if (session()->has('contributions') && count($this->getConstributions()) > 0) {
			// We have things in the session
			// Clear the session befor continuing
			$this->clearAll();
		}
		// Get the institution by its id
		$members             = $this->institution->with(['members' => function($query){
			 						$query->where('status','<>','left');
								}])->find($institutionId)->members;
		$toSetMembers        = [];
		// 1. If this is bulk withdraw then set them in a special way
		if ($this->getContributionType() === 'BULK_WITHDRAW' ) {
			// Pluck members into one dimension array to facilitate upload
			$this->setWithdrawMembers($members->pluck('adhersion_id'));
			return true;
		}

		foreach ($members as $member) {
			$member->monthly_fee = (int) $member->monthly_fee;
			$toSetMembers[]      = $member;
		}
		$this->setContributions($toSetMembers);
	}

    /**
	 * Set members
	 * @param integer $memberId
	 *
	 * @return bool
	 */
	public function setMember($memberToSet = array())
	{	
		$members = array();
		// Check if the provided parameter is an id for one member or not
		if (!is_array($memberToSet) && is_numeric($memberToSet)) {
			$member              = $this->member->notLeft()->findOrFail($memberId);
			$member->monthly_fee = (int) $member->monthly_fee;
			$member->timestamps  = false;
			$members[]           = $member->toArray();

		    $this->setContributions($members);
			return true;
		}

		// Check if it's closing module
		$isClosingExercise = in_array($this->getContributionType(),['ANNUAL_INTEREST','BULK_WITHDRAW']);

		// We have many members to upload
		if (is_array($memberToSet)) {
			$rowsWithErrors  		 = [];
			$rowsWithSuccess 		 = [];
			$rowsWithDifferentAmount = [];



			foreach ($memberToSet as $member) {
				    if ((!isset($member[0]) || !isset($member[1])) && !$isClosingExercise) {
				    	$rowsWithErrors[] = $member;
				    	continue;
				    }
               	
               	try{
					$memberFromDb              = $this->member->findByAdhersion($member[0]);

					$memberFromDb->monthly_fee = (int) $memberFromDb->monthly_fee;
					$memberFromDb->timestamps  = false;
					$memberFromDb->institution = $memberFromDb->institution->name;
				    // Does contribution look same as the one registered
				    if ($memberFromDb->monthly_fee !== (int) $member[1]) {
				    	$memberFromDb->monthly_fee = (int) $member[1];
				    	$rowsWithDifferentAmount[] = $memberFromDb;
				    }

				    // If this is closing update monthly fees
				    $rowsWithSuccess[] = $memberFromDb;
				}
				catch(\Exception $ex){
						$member[] = $ex->getMessage().'| This member does not exist in our database';
						$rowsWithErrors[] = $member;
				}
			}	
		}

		$rowsWithErrors  		  = new Collection($rowsWithErrors);
		$rowsWithDifferentAmount  = new Collection($rowsWithDifferentAmount);
		$rowsWithSuccess  		  = new Collection($rowsWithSuccess);
		
		if (!$rowsWithErrors->isEmpty() && $this->getContributionType() === 'OTHER') {
		      $message = 'We have identified '.$rowsWithErrors->count().' member(s) with wrong format, therefore we did not consider them.';		
		      flash()->error($message);	
		}

		if (!$rowsWithDifferentAmount->isEmpty() && $this->getContributionType() === 'OTHER') {
		      $message = 'We have identified '.$rowsWithDifferentAmount->count().' member(s) with  diffent contributions amount.';	
		   	  
		   	  flash()->error($message);
		}

		session()->put('contributionsWithDifference',$rowsWithDifferentAmount);
		session()->put('uploadsWithErrors', $rowsWithErrors);

		$this->setContributions($rowsWithSuccess->toArray());
		return true;
	}

    /**
	 * Set members who are about to contribute
	 * @param array $members
	 */
	public function setContributionMembers(array $members) {
		session()->put('contributionMembers', $members);
	}

	/**
	 * setContributions description
	 * @param array $data
	 */
	public function setContributions(array $data) {
		$finalData = [];
		foreach ($data as $item) {
			$item['institution'] = $this->institution->find($item['institution_id'])->name;
			$finalData[]         = $item;
		}
		return session()->put('contributions', $finalData);
	}

	/**
	 * Set contribution recurrence to the session
	 * @param  $recurrence 
	 */
	public function setRecurrence($recurrence)
	{
		session()->put('contribution.recurrence',$recurrence);
	}

	/**
	 * Set contribution recurrence to the session
	 * @param  $recurrence 
	 */
	public function getRecurrence()
	{
		return session()->get('contribution.recurrence');
	}

	/**
	 * Set Interest Year for contribution
	 * @param string $year 
	 */
	public function setInterestYear(string $year)
	{
		session()->put('interest-year',$year);
		// Automatically set members when we are withdrawing
		if ($this->getContributionType() === 'BULK_WITHDRAW') {
			// First set this transaction to withdraw
			$this->setTransactionType('withdrawal');
			// Load member who should have interest
			$this->setMemberByInterestYear($year);
		}
	}

	/**
	 * Set members by interesting Year
	 * @param  $year 
	 */
	public function setMemberByInterestYear($year)
	{
		$contributions = $this->contribution
						->canReceivedInterestFor($year)
						->get(['adhersion_id','amount']);

		$widthraws = [];
        // Set these contributions for the withdraw
        foreach ($contributions as $contribution) {
			$member              = $this->member->findByAdhersion($contribution->adhersion_id);
			$member->monthly_fee = (int) $contribution->amount;
			$member->timestamps  = false;
			$widthraws[]         = $member->toArray();
        }
        
 		$this->setContributions($widthraws);
	}

	/**
	 * Get Interest year
	 * @return string 
	 */
	public function getInterestYear()
	{
		return session()->get('interest-year',date('Y'));
	}

	/**
	 * Set transactions Type
	 * @param  $transactionType 
	 */
	public function setTransactionType($transactionType)
	{
		session()->set('transaction-type',$transactionType);
	}
	/**
	 * Get transaction type
	 * @return string
	 */
	public function getTransactionType()
	{
		return session()->get('transaction-type','saving');
	}

	/**
	 * Set members for withdraw
	 * @param arrayCollection $members 
	 */
	public function setWithdrawMembers(arrayCollection $members)
	{
		$year = $this->getInterestYear();

    	// 1. Get first all people with interest for this Year
    	$receivedInterest = $this->contribution
    	                         ->whereIn('adhersion_id',$members->toArray())
    							 ->receivedInterestFor($year)->get();

    	// // 2. Get all members who have withdrew interest for this year
    	$withdrewInterest = $this->contribution
    	                         ->whereIn('adhersion_id',$members->toArray())
    							 ->withdrewInterestFor($year)->get();

    	// 3. Remove those who received their interest
    	$eligibleMembers  = $receivedInterest->filter(function($member) use($withdrewInterest){
    		return $withdrewInterest->where('adhersion_id',$member->adhersion_id)->isEmpty();
    	});
    	
    	// 5. Get information
    	if ($eligibleMembers->isEmpty()) {
	        flash()->error(trans('Mentioned member do not have eligible member for this year interest'));
    	}

		$widthraws = [];
        // Set these contributions for the withdraw
        foreach ($eligibleMembers as $contribution) {
			$member              = $this->member->findByAdhersion($contribution->adhersion_id);
			$member->monthly_fee = (int) $contribution->amount;
			$member->timestamps  = false;
			$widthraws[]         = $member->toArray();
        }
        
 		$this->setContributions($widthraws);

 		return collect($widthraws);
	}

	/**
	 * Set wording for the current contribution
	 * 
	 * @param void
	 */
	public function setWording($wording)
	{
		session()->put('contribution_wording', $wording);
	}

	/**
	 * Get wording for current contributionsession
	 * 
	 * @return string
	 */
	public function getWording()
	{

		if (in_array($this->getContributionType(),['ANNUAL_INTEREST','BULK_WITHDRAW'])) {
			return $this->getContributionType().'_'.$this->getInterestYear();
		}
		
		return session()->get('contribution_wording', null);
	}

	/**
	 * Set Contribution Type
	 * 
	 * @return string
	 */
	public function setContributionType(string $type)
	{
		 session()->set('contributions-type', $type);

		 // Set transaction type based on set transaction type
		 switch ($type) {
		 	case 'ANNUAL_INTEREST':
		 		 $this->setTransactionType('saving');
		 		break;
		 	case 'BULK_WITHDRAW':
		 		 $this->setTransactionType('withdrawal');
		 		break;		 	
		 }
	}

	/**
	 * Get contribution type
	 * 
	 * @return string
	 */
	public function getContributionType()
	{
		return session()->get('contributions-type', null);
	}
	/**
	 * Get all contributions as per the current session
	 * @return array
	 */
	public function getConstributions() {
		return new Collection(session()->get('contributions'));
	}

	/**
	 * Get contributions with differences
	 * 
	 * @return [type] [description]
	 */
	public function getConstributionsWithDifference()
	{
		return new Collection(session()->get('contributionsWithDifference'));
	}

	/**
	 * Get uploaded contribution with erros
	 * @return Collection 
	 */
	public function getUploadWithErros()
	{
		return new Collection(session()->get('uploadsWithErrors'));
	}

	/**
	 * Update a single monthly contribution for a given uses
	 * @param  [type] $adhersion_number [description]
	 * @param  [type] $newValue         [description]
	 * @return [type]                   [description]
	 */
	public function updateMonthlyFee($adhersion_number, $newMontlyFee) {
		// First get what is in the session now
		$data = $this->getConstributions()->toArray();
		// in (PHP 5 >= 5.5.0) you don't have to write your own function to search through a multi dimensional array
		$key = $this->searchAdhersionKey($adhersion_number, $data);

		// An array can have index 0 that's why we check if it's not strictly false		
		if ($key !== false) {
			$data[$key]['monthly_fee'] = (int) $newMontlyFee;
		}

		// Now we are ready to go
		return $this->setContributions($data);
	}

	/**
	 * Remove one member from current contribution session
	 * 
	 * @param  numeric $memberId
	 * @return void
	 */
	public function removeMember($adhersion_number)
	{
      $adhersion_number = (int) $adhersion_number;

	  $filtered = $this->getConstributions()->filter(function($member)  use ($adhersion_number){
	  	  if ($member['adhersion_id'] == $adhersion_number) {
	  	  	flash()->warning($member['first_name'].' '.$member['last_name'].'('.$adhersion_number.')'.trans('contribution.has_been_removed_from_current_contribution_session'));
	  	  	return false;
	  	  }

	  	  return true;
	  });

	  $this->setContributions($filtered->toArray());	
	}

	/**
	 * Get total Montly fees
	 * @return number
	 */
	public function total() {
		$content = $this->getConstributions();
		$sum = 0;
		if (count($content) < 1) {
			return $sum;
		}

		// now calculate all amount we have
		foreach ($content as $item) {
			$sum = $sum + $item['monthly_fee'];
		}
		return $sum;
	}

	/**
	 * Set debit account
	 * @param mixed $accountId
	 */
	public function setDebitAccount($accountId) {
		return session()->set('debit_account', $accountId);
	}

	/**
	 * GetDebit account id
	 * @return Integer
	 */
	public function getDebitAccount() {

		$defaultDebitAccount	= DefaultAccount::with('accounts')
								    ->debit()
								    ->batchContribution()
								    ->first();

		// Set default account if it is not yet set
		if (empty(session()->get('debit_account'))) {
			$this->setDebitAccount(intval($defaultDebitAccount->account_real_id));
		}

		return session()->get('debit_account');
	}
	/**
	 * Set credit account in the session
	 * @param integer $accountId Account ID to be credited
	 */
	public function setCreditAccount($accountId) {
		return session()->set('credit_account', $accountId);
	}
	/**
	 * get current set credited account
	 * @return [type] [description]
	 */
	public function getCreditAccount() {
		
		$defaultCreditAccount	=  DefaultAccount::with('accounts')
												 ->credit()
												 ->batchContribution()
												 ->first();

		// Set default account if it is not yet set
		if (empty(session()->get('credit_account'))) {
			$this->setCreditAccount(intval($defaultCreditAccount->account_real_id));
		}

		return session()->get('credit_account'); // Here we assume the account with id 26 is default
	}

	/**
	 * Set the month we are paying for
	 * @param mixed $month
	 */
	public function setMonth($month) {
		return session()->put('month', $month);
	}

	/**
	 * Get the contribution month
	 * @return integer
	 */
	public function getMonth() {
		return session()->get('month', 0);
	}

	/**
	 * Set the institution
	 * @param mixed $institutionId
	 */
	public function setInstitution($institutionId) {
		return session()->put('contribution_institution', $institutionId);
	}

	/**
	 * get the current contribution institutions
	 * @return ID
	 */
	public function getInstitution() {
		return session()->get('contribution_institution', 0); // We assume institution 1 is dhe default one
	}

	/**
	 * Forget contribution with differences
	 * 		
	 * @return void
	 */
	public function forgetWithDifferences()
	{
		$withDifference = $this->getConstributionsWithDifference();
		$members        = $this->getConstributions();

		$members = $members->filter(function($member) use($withDifference){
		    return $withDifference->where('adhersion_id',$member['adhersion_id'])->isEmpty();
		});

		flash()->success($this->getConstributions()->count() - $members->count() . trans('contribution.members_are_removed_from_this_contribution_session'));

		$this->setContributions($members->toArray());
		$this->forgetContributionWithDifferences();
	}

	/**
	 * Remove contribution with differencdes
	 * 
	 * @return void
	 */
	public function forgetContributionWithDifferences()
	{
		session()->forget('contributionsWithDifference');
	}
	/**
	 * Clear all present SessionIds
	 * @return void
	 */
	public function clearAll() {
		session()->forget('contributions');
		session()->forget('debit_account');
		session()->forget('credit_account');
		session()->forget('month');
		session()->forget('contribution_institution');
		session()->forget('uploadsWithErrors');
		session()->forget('contributionsWithDifference');
		session()->forget('contribution_wording');
		session()->forget('contributions-type');
		session()->forget('interest-year');
	}
}