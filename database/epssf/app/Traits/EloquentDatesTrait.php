<?php 
namespace Ceb\Traits;

trait EloquentDatesTrait {
	 /**
     * Get records before a given date
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBefore($query,$date)
    {
    	return $query->where('created_at','<=',$date);
    }
    
    /**
     * Get record after a given date
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAfter($query,$date)
    {	
    	return $query->where('created_at','>=',$date);
    }

    /**
	 * Get contribution between two dates
	 * 
	 * @param  eloquent $query 
	 * @param  string   $date1 start date
	 * @param  string   $date2 end date
	 * @return $this   
	 */
	public function scopeBetweenDates($query,$date1,$date2)
    {
    	$date1 = date('Y-m-d',strtotime($date1)).' 00:00:00';
    	$date2 = date('Y-m-d',strtotime($date2)).' 23:59:59';

        return $query->whereBetween('created_at', [$date1, $date2]);
    }

    /**
     * Get record after a given id
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFor($query,$id)
    {
    	return $query->where('id',$id);
    }

}
