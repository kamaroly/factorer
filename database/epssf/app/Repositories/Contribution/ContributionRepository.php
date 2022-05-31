<?php
namespace Ceb\Repositories\Contribution;

use DB;
use Sentry;
use Ceb\Models\User;
use Ceb\Models\Posting;
use Ceb\Models\AssetType;
use Ceb\Models\Contribution;
use Ceb\Traits\TransactionTrait;
use Ceb\Factories\ContributionFactory;


/**
 * Contribution Repository
 */
class ContributionRepository {
	use TransactionTrait;
	/** @var  hosts the instance of contribution model */
	protected $contribution;
	function __construct(Contribution $contribution, 
						 AssetType $assetType, 
						 ContributionFactory $contributionFactory
						) 
	{
		$this->contribution        = $contribution;
		$this->assetType           = $assetType;
		$this->contributionFactory = $contributionFactory;
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param $data
	 *
	 * @return BaseResponse
	 */
	public function store($data) {
		return $this->constribution->store($data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $data
	 *
	 * @return BaseResponse
	 */
	public function update($data) {
		return $this->contribution->update($data);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return BaseResponse
	 */
	public function destroy($id) {
		$this->contribution->findOrfail($id);
		return $this->contribution->delete();
	}

	/**
	 * Return all the registered users
	 *
	 * @return Collection
	 */
	public function all() {
		return $this->contribution->all();
	}

	public function paginate($page = 10) {
		return $this->contribution->paginate($page);
	}
	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed $identifier
	 *
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveById($identifier) {
		return $this->contribution->findOrfail($identifier);
	}

	/**
	 * Complete current transactions in contribution
	 *
	 * @return  bool
	 */
	public function complete() {
		$transactionId = $this->getTransactionId();
		// Start saving if something fails cancel everything
		Db::beginTransaction();

		$saveContibution = $this->saveContibutions($transactionId);
		$savePosting     = $this->savePostings($transactionId);

		// Rollback the transaction via if one of the insert fails
		if (!$saveContibution || !$savePosting) {
			DB::rollBack();
			return false;
		}

		// Lastly, Let's commit a transaction since we reached here
		DB::commit();
		return $transactionId;

	}

	/**
	 * Saving contribution as per contribution factory
	 *
	 * @return bool
	 */
	private function saveContibutions($transactionId) {

		# Get data in the factory
		$month            = $this->contributionFactory->getMonth();
		$contributionType = $this->contributionFactory->getContributionType();
		$contributions    = $this->contributionFactory->getConstributions()->toArray();
		$fullTransactions = true;

		$memberWithoutFees = 0;
		foreach ($contributions as $contribution) {
			if (empty($contribution['monthly_fee'])) {
				$memberWithoutFees++;
				continue;
			}

			$contribution['transactionid']      = $transactionId;
			$contribution['month']              = $month;
			$contribution['institution_id']     = $contribution['institution_id'];
			$contribution['amount']             = $contribution['monthly_fee'];
			$contribution['state']              = 'Ancien';
			$contribution['year']               = date('Y');
			$contribution['contract_number']    = $this->getContributionContractNumber();
			$contribution['transaction_type']   = $this->contributionFactory->getTransactionType();
			$contribution['transaction_reason'] = 'Montly_contribution';
			$contribution['wording']            = $this->contributionFactory->getWording();
			
			// Custom transactions reasons
			if (in_array($contributionType,['ANNUAL_INTEREST','BULK_WITHDRAW'])) {
				$contribution['transaction_reason'] = $contribution['wording'];
			}

			//Remove unwanted column
			unset($contribution['id']);

			# try to save if it doesn't work then
			# exist the loop
			if (!Contribution::create($contribution)) {
				return false;
			}

			// If the loan we are paying for has cautionneur, then make sure
			// We update our member cautionneur table by adding the amount
			// paid by this member to the refund amount as long 
			// as cautionneur still have a balance
			// DISABLED AS REQUESTED BY OLIVIER IN THE FOLLOWING DOCUMENT ON POINT #4
			// https://docs.google.com/document/d/1D8Qhq4P6l8qw01Ib_6hXxdQF9XBBokf3-fhpPSlFfzs/edit?ts=5efe0598
			// $loan = User::byAdhersion($contribution['adhersion_id'])->first();

			// if(!empty($loan) AND !is_null($loan->latest_loan)){
			// 	if(! loanGuarantorReducer($loan->latest_loan,$contribution['amount']) ){
			// 		return false;
			// 	}	
			// }
		
		}

		if (!empty($memberWithoutFees)) {
			flash()->warning($memberWithoutFees. trans('contribution.members_could_not_contribute_because_they_have_0_fee'));
		}
		return true;
	}

	/**
	 * Save posting to the database
	 * @param  STRING $transactionId UNIQUE TRANSACTIONID
	 * @return bool
	 */
	private function savePostings($transactionId) {

		$debitAccount = $this->contributionFactory->getDebitAccount();
		$creditAccount = $this->contributionFactory->getCreditAccount();
		
		// First prepare data to use for the debit account
		// Once are have debited(deducted data) then we can
		// Credit the account to be credited
		$posting['transactionid'] = $transactionId;
		$posting['account_id'] = $debitAccount;
		$posting['journal_id'] = 1; // We assume per default we are using journal 1
		$posting['asset_type'] = null;
		$posting['amount'] = $this->contributionFactory->total();
		$posting['user_id'] = Sentry::getUser()->id;
		$posting['account_period'] = date('Y');
		$posting['transaction_type'] = 'Debit';
		$posting['wording']			 = \Input::input('wording');
		// Try to post the debit before crediting another account
		$debiting = Posting::create($posting);

		// Change few data for crediting
		// Then try to credit the account too
		$posting['transaction_type'] = 'Credit';
		$posting['account_id'] = $creditAccount;

		$crediting = Posting::create($posting);

		if (!$debiting || !$crediting) {
			
			return false;
		}
		// Ouf time to go to bed now
		return true;
	}
}