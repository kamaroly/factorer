<?php
namespace Ceb\Models;
use Ceb\Models\Model;
use Ceb\Models\User as Member;;
use Illuminate\Support\Facades\DB;

class MonthlyFeeInventory extends Model {

	protected $table = 'monthly_fee_inventory';

	/**
	 * Member of this Monthly Fee inventory
	 * 
	 * @return Collection | User
	 */
	public function member()
	{
		return $this->belongsTo(Member::class,'adhersion_id','adhersion_id');
	}
	
	/**
	 * Get history of the monthly fees
	 * @param  string $startDate start date in format of YYYY-MM-DD
	 * @param  string $endDate   end date in format of YYYY-MM-DD
	 * @return array|object
	 */
	public static function history($startDate,$endDate)
	{

		
		$query = "SELECT 
					  date(m.updated_at) dates,
				      m.type AS type,
				      a.adhersion_id,
					  CONCAT(first_name,' ',last_name) names,
					  first_name,
					  last_name,
					  a.status,
				      i.name AS institution,
				      a.service,
				      m.amount
					FROM users a
						LEFT JOIN 
					     institutions AS i ON a.institution_id = i.id
						LEFT JOIN 
				         monthly_fee_inventory AS m ON a.adhersion_id = m.adhersion_id
				     WHERE m.amount IS NOT NULL AND date(m.updated_at) BETWEEN ? AND ?";

	    return DB::select($query, [$startDate,$endDate]);
	}
}