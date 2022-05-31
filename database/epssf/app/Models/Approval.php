<?php

namespace Ceb\Models;

use Ceb\Events\CorrectionStatusChanged;
use Illuminate\Database\Eloquent\Model;
use Ceb\Exceptions\InavalidApprovalStatusException;

class Approval extends Model
{
	/**
	 * Allowed statuses
	 * @var array
	 */
	private   $allowedStatus   = ['approved','rejected','pending'];

	/**
	 * Allowed fields to be mas filled
	 * @var array
	 */
    protected $fillable = ['nature','transactionid','type','content','status','approvers','replaced_content'];

    /**
     * Filter non rejected 
     * @param   $query 
     * @return  $this
     */
    public function scopeNotRejected($query)
    {
    	return $query->where('status','<>','rejected');
    }

    /**
     * Get get Non APPROVEd
     * @param   $query 
     * @return  
     */
    public function scopeNotApproved($query)
    {
    	return $query->where('status','<>','approved');
    }

    /**
     * Change status of the approval
     * @param   $status 
     * @return  $this
     */
    public function changeStatus($status)
    {
    	if (!in_array($status,$this->allowedStatus)) {
    		throw new InavalidApprovalStatusException("Invalid status for approval passed ".$status, 1);
    	}

        // Automatic rejection of the request if we can't find 
        // corresponding entity
        if (empty(Loan::byTransaction($this->transactionid)->first())) {
            flash()->warning(trans('general.we_can_not_find_transaction_you_want_to_process'));
            $status = 'rejected';
        }

    	$this->status = $status;
    	
    	if ($saved = $this->save()) {
			 // Let everyone know that this has been updated...
	        event(new CorrectionStatusChanged($this));
	        return $saved;
    	}

    	return false;
    }
}