<?php

namespace Modules\Accounting\Models;

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
