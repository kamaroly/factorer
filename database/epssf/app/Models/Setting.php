<?php

namespace Ceb\Models;

class Setting extends Model {
	
	protected  $table = 'settings';

	/**
	 * Rate for give months
	 * @param   $query 
	 * @param   $start 
	 * @param   $end   
	 * @return         
	 */
    public function scopeKeyValue($query,$key)
    {
    	return $query->where('key',$key)->first()->value;
    }

    public function get($key)
    {
        $results = $this->where('key',$key);
        if ($results->count() > 0 ) {
            return $results->first()->value;
        }
        return null;
    }
    /**
     * Determine if the key exists
     * @param  $query 
     * @param   $key   
     * @return  
     */
    public function hasKey($key)
    {
    	return $this->where('key',$key)->count() > 0;
    }
}
