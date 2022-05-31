<?php
namespace Ceb\ViewComposers;

use Ceb\Models\Journal;
use Illuminate\Contracts\View\View;

/**
 * journalViewComposer
 */
class JournalViewComposer {
	public $journal;
	function __construct(Journal $journal) {
		$this->journal = $journal;
	}

	public function compose(View $view) {
		$view->with('journals', $this->journal->lists('name', 'id'));
	}
}