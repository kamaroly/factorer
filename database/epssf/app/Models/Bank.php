<?php

namespace Ceb\Models;

class Bank extends Model {

	/**
	 * Member per institution
	 * 
	 * @return object
	 **/
	public function members() {
		return $this->hasMany('Ceb\Models\User');
	}
    
    /**
     * n order to let Eloquent eager load this thing, we must create a method returning Relation object. And here it is:
     * @return object
     */
	public function memberCount()
	{
	 return $this->members()->count();
	}

	/**
	 * Get loan per insitutions
	 * @return [type] [description]
	 */
	public function loans()
	{
		return $this->members()->with('loans');
	}

	/**
	 * Get loan count 
	 * @return int 
	 */
	public function loanCount()
	{
		return $this->loans()->count();
	}
	/**
	 * Get member with loan
	 * @return Eloquent Object
	 */
	public function membersWithLoan() {
		$membersWithLoans = [];
		/** Make sure we only get member with loan */
		foreach ($this->members as $member) {

			if ($member->has_active_loan == true) {
				$membersWithLoans[] = $member;
			}
		}
		return $membersWithLoans;
	}

	/**
	 * Get list of members with No active Loan
	 * @return Object of members
	 */
	public function membersWithNoLoan() {
		$membersWithNoLoan = [];
		/** Make sure we only get member with loan */
		foreach ($this->members as $member) {
			if (!$member->hasActiveLoan()) {
				$membersWithNoLoan[] = $member;
			}
		}
		return $membersWithNoLoan;
	}
}
