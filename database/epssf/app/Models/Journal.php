<?php

namespace Ceb\Models;
class Journal extends Model {

	/**
	 * Relationshp with posting
	 * @return \Ceb\Models\Posting
	 */
	public function postings()
	{
		return $this->hasMany('\Ceb\Models\Posting');
	}
}
