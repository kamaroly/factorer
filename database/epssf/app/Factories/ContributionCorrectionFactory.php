<?php 

namespace Ceb\Factories;

use Ceb\Models\User;
use Ceb\Models\Posting;
use Ceb\Models\Contribution;
use Illuminate\Support\Collection;
use Ceb\Events\ContributionDeleted;
use Ceb\Events\ContributionAdjusted;

class ContributionCorrectionFactory	
{
	
	/**
	 * Set contribution in the session
	 * @param Collect $collection 
	 */
	public function setContributions(Collection $collection)
	{
		$collection = $collection->filter(function($item){
			return count($item) == 9;
		});

		return session()->put('contributions-to-correct',$collection);
	}
	/**
	 * Get contributions 
	 * @return collection of contributions
	 */
	public function contributions()
	{
		return session()->get('contributions-to-correct',collect([]));
	}

	/**
	 * Add contribution to existing session
	 * @param  $adhersionId 
	 * @param  $amount      
	 */
	public function add($adhersionId,$amount)
	{
        $adhersionId           = request()->get('adhersion_id');
		$contributionToCorrect = $this->contributions();

        // 1. Don't add this adhersion if it is already in the session
        if (empty($contributionToCorrect->where('adhersion_id',$adhersionId)->first()) === false) {
            flash()->warning(trans('general.adhersion_id_already_exists_for_this_transaction', 
                ['adhersion_id' => $adhersionId]));
            return false;
        }

        // 2. Confirm that adhersion number exists.
        $member = User::byAdhersion($adhersionId)->first();

        if (! $member->exists() ) {
            // 2. If exists, then add it to the existing session with flag of new.
            flash()->error(trans('general.invalid_adhersionid', ['adhersion_id' => $adhersionId]));
            return false;
        }

        $contributionToCorrect->prepend([
                    'id'            => null,
                    'amount'        => (int) request()->get('amount'),
                    'adhersion_id'  => (int) $adhersionId,
                    'nid'           => $member->member_nid,
                    'institution'   => $member->institution->name,
                    'employee_id'   => $member->employee_id,
                    'names'         => $member->first_name.' '.$member->last_name,
                    'transactionid' => $contributionToCorrect->first()['transactionid'],
                    'action'        => 'ADD',
                ]);

        // Add this to the session
        return $this->setContributions($contributionToCorrect);
	}
	/**
	 * Remove transaction from database
	 * @return boolean
	 */
	public function removeTransaction()
	{
			$transactionid        = $this->contributions()->first()['transactionid'];

			$contributions        = Contribution::byTransaction($transactionid);
			$deletedContribution = $contributions->get();

            if ($contributions->delete()) {
                // Remove all postings related to the deleted contribution
                if (! $deletedContribution->isEmpty()) {
                    Posting::byTransaction($deletedContribution->first()->transactionid)->delete();
                }

                (new ContributionDeleted($deletedContribution));
                flash()->success(trans('general.contribution_removed_from_our_record_and_cannot_be_restored'));
                return $this->clear();
            }
            return false;
	}

	/**
	 * Edit item in the current session
	 * @param   $adhersionId 
	 * @return          
	 */
	public function edit($adhersionId)
	{
        $contributions          = $this->contributions();
        $adhersionId           = (int) $adhersionId;

        // Get the key to replace with
        $index                  = $contributions->where('adhersion_id', $adhersionId)->keys()->first();
        $contribution           = $contributions->where('adhersion_id', $adhersionId)->first();
        $contribution['amount'] = (int) request()->get('amount');
        $contribution['action'] = 'UPDATE';

        // Update content of the session
        $contributions[$index] = $contribution;
        $this->setContributions($contributions);
	}

	    /**
     * Complete contribution correction
     * @return  
     */
    public function complete()
    {
         $contributions        = $this->contributions();
         
         // Make a copy of the repo
         $existingContribution = Contribution::where('transactionid',$contributions->first()['transactionid'])
                                 ->first();
        
        $newContribution             = $existingContribution->replicate();
        $newContribution->created_at = $existingContribution->created_at;

        $contributions->each(function($contribution) use($newContribution) {
            // Get fresh copy from database
            $contributionInDb = Contribution::find($contribution['id']);     
            // Go to next if we don't have this in our DB
            if(empty($contributionInDb) && $contribution['action'] !== 'ADD'){
                return false;
            }
                // Determine what to do on this contribution
                switch ($contribution['action']) {
                    case 'REMOVE':
                        // Buffer the account to delete first
                        $deletedContribution = $contributionInDb;
                        // Remove contribution
                        $contributionInDb->delete();
                        // Fire events
                        (new ContributionDeleted($deletedContribution));
                        break;
                    case 'ADD': # This didn't exist we are adding it
                        $newContribution->amount       = $contribution['amount'];
                        $newContribution->adhersion_id = $contribution['adhersion_id'];
                        $newContribution->wording      = 'contribution_ADDED_TO_EXISTING_TRANSACTION_BY_'.\Sentry::getUser()->email;
                        $newContribution->save();
                        event(new contributionAdjusted($contributionInDb));
                        break;
                    case 'UPDATE':
                        $contributionInDb->amount = $contribution['amount'];
                        $contributionInDb->wording      = 'contribution_CORRECTED_BY_'.\Sentry::getUser()->email;
                        $contributionInDb->save();

                        event(new ContributionAdjusted($contributionInDb));
                        break;
                }
        });

        // Remove this from session
        $this->clear();
        flash()->success(trans('general.contribution_adjusted'));
        return true;
    }

	/**
	 * Remove item from current session
	 * @param   $adhersionId 
	 * @return    
	 */
	public function remove($adhersionId)
	{
        $contributions          = $this->contributions();
        $adhersionId           = (int) $adhersionId;
        // Get the key to replace with
        $index                  = $contributions->where('adhersion_id', $adhersionId)->keys()->first();
        $contribution           = $contributions->where('adhersion_id', $adhersionId)->first();
        $contribution['action'] = 'REMOVE';

        // Update content of the session
        $contributions[$index] = $contribution;
        
        return $this->setContributions($contributions);
	}
	/**
	 * Cancel this transaction
	 * @return  void
	 */
	public function clear()
	{
		session()->forget('contributions-to-correct');
	}
}