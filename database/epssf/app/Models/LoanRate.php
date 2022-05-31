<?php

namespace Ceb\Models;
use Ceb\Models\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LoanRate extends Model {
	
	protected  $table = 'loan_rates';

	/**
	 * Rate for give months
	 * @param   $query 
	 * @param   $start 
	 * @param   $end   
	 * @return         
	 */
    public function rate($monthNumber)
    {
    	$results = DB::select('select rate from `loan_rates` where (? >=`start_month` and  ?<= `end_month`)', [$monthNumber,$monthNumber]);
    	$resultsCollection = new Collection($results);
        
    	// If the rate is not configured in the system then return 0
    	if ($resultsCollection->isEmpty()) {
    		$results = DB::select('select max(rate) from `loan_rates`');
            $resultsCollection = new Collection($results);  
            return $monthNumber;        
    	}

    	return $resultsCollection->first()->rate;
    }
}
