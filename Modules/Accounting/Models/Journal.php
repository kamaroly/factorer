<?php

namespace Modules\Accounting\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model {

	/**
	 * Relationshp with posting
	 * @return \Ceb\Models\Posting
	 */
	public function postings()
	{
		return $this->hasMany(Posting::class);
	}
}
