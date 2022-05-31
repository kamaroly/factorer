<?php  

namespace Ceb\Factories;

use Ceb\Models\Contribution;

class ContributionReconciliationFactory{

	public function reconcile()
	{
		// 1. Get Contributions in the db
		$this->dbContributions();

		// 2. Get Contributions in the csv
		$this->csvContributions();

		// 3. Get transactions that both in DB and CSV
		$this->existsInBoth();

		// 4. Mark transactions that are in DB only 
		$this->onlyInDb();
		// 5. Mark transactions that are in CSV only 
		$this->onlyInCsv();
		// 6. Makr transactions that are not matching
		$this->amountNotMatching();
	}

	/**
	 * Transactions that exists in both db and csv
	 * @return  collection
	 */
	private function existsInBoth(){
		$inBoth = collect([]);
		foreach ($this->dbContributions() as $key => $item) {

			if ($inCsvItem = $this->existsInCsv($item)) {
				$item['amount_in_csv'] = $inCsvItem['amount'];
				$inBoth->push($item);
			}
		}

		// Keep results
		session()->put('contributions-in-both',$inBoth);

		return $inBoth;
	}

	/**
	 * Set Amount not matching
	 * @return collection  
	 */
	public function amountNotMatching()
	{
		$notMatching = collect([]);

		foreach($this->getInBoth() as $key => $item){
			if ($item['amount'] !== $item['amount_in_csv']) {
				$notMatching->push($item);
			}
		}
		session()->put('contributions-not-matching',$notMatching);
		return $notMatching;
	}

	/**
	 * Get not matching Contributions
	 * @return  
	 */
	public function getNotMatchingContribution()
	{
		return session()->get('contributions-not-matching',collect([]));
	}
	/**
	 * Transactions that exists in both db and csv
	 * @return  collection
	 */
	private function onlyInDb(){
		$onlyInDb = collect([]);
		foreach ($this->dbContributions() as $key => $item) {

			if (! $this->existsInCsv($item)) {
				$onlyInDb->push($item);
			}
		}

		// Keep results
		session()->put('contributions-only-db',$onlyInDb);

		return $onlyInDb;
	}

	/**
	 * Transactions that exists in both db and csv
	 * @return  collection
	 */
	private function onlyInCsv(){
		$onlyInCsv = collect([]);
		foreach ($this->csvContributions() as $key => $item) {

			if (! $this->existsInDb($item)) {
				$onlyInCsv->push($item);
			}
		}
		// Keep results
		session()->put('contributions-only-csv',$onlyInCsv);
		return $onlyInCsv;
	}

	/**
	 * Get Contribution only in Both
	 */
	public function getInBoth()
	{
		return 	session()->get('contributions-in-both',collect([]));
	}

	/**
	 * Get Contribution only in Database
	 */
	public function getOnlyInDb()
	{
		return 	session()->get('contributions-only-db',collect([]));
	}

	/**
	 * Get Contribution only in CSV
	 */
	public function getOnlyInCsv()
	{
		return 	session()->get('contributions-only-csv',collect([]));
	}
	/**
	 * Check if item exists in csv
	 * @param  array  $item 
	 * @return array
	 */
	private function existsInCsv(array $item){
		$item = $this->csvContributions()->where('adhersion_id',(int) $item['adhersion_id'])->first();

		if (empty($item)) {
			return false;
		}
		return $item;
	}

	/**
	 * Check if item exists in csv
	 * @param  array  $item 
	 * @return array
	 */
	private function existsInDb(array $item){
		$item = $this->dbContributions()->where('adhersion_id',$item['adhersion_id'])->first();

		if (empty($item)) {
			return false;
		}
		return $item;
	}
	/**
	 * Set Contributions in the current sessions
	 * @param  $Contributions 
	 */
	public function setDbContributions($Contributions)
	{
		session()->put('contributions-from-db',collect($Contributions));
	}

	/**
	 * Set Contributions from uploaded CSV
	 * @param  $Contributions 
	 */
	public function setCsvContributions($Contributions)
	{
		session()->put('contributions-from-csv',collect($Contributions));
	}

	/**
	 * Get Contributions for corrections
	 * @return  collection
	 */
	public function dbContributions()
	{
		return session()->get('contributions-from-db',collect([]));
	}

	/**
	 * Get Csv Contributions for corrections
	 * @return  collection
	 */
	public function csvContributions()
	{
		return session()->get('contributions-from-csv',collect([]));
	}

	/**
	 * Remove all items in the session
	 * @return void
	 */
	public function clear()
	{
		session()->forget('contributions-from-db');
		session()->forget('contributions-in-both');
		session()->forget('contributions-not-matching');
		session()->forget('contributions-only-db');
		session()->forget('contributions-only-csv');
		session()->forget('contributions-from-db');
		session()->forget('contributions-from-csv');
	}
}