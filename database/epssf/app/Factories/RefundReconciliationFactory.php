<?php  

namespace Ceb\Factories;

use Ceb\Models\Refund;

class RefundReconciliationFactory{

	public function reconcile()
	{
		// 1. Get refunds in the db
		$this->dbRefunds();
		// 2. Get refunds in the csv
		$this->csvRefunds();
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


		foreach ($this->dbRefunds() as $key => $item) {

			if ($inCsvItem = $this->existsInCsv($item)) {
				$item['amount_in_csv'] = $inCsvItem['amount'];
				$inBoth->push($item);
			}
		}
		// Keep results
		session()->put('refunds-in-both',$inBoth);

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
		session()->put('refunds-not-matching',$notMatching);
		return $notMatching;
	}

	/**
	 * Get not matching refunds
	 * @return  
	 */
	public function getNotMatchingRefund()
	{
		return session()->get('refunds-not-matching',collect([]));
	}
	/**
	 * Transactions that exists in both db and csv
	 * @return  collection
	 */
	private function onlyInDb(){
		$onlyInDb = collect([]);
		foreach ($this->dbRefunds() as $key => $item) {

			if (! $this->existsInCsv($item)) {
				$onlyInDb->push($item);
			}
		}

		// Keep results
		session()->put('refunds-only-db',$onlyInDb);

		return $onlyInDb;
	}

	/**
	 * Transactions that exists in both db and csv
	 * @return  collection
	 */
	private function onlyInCsv(){
		$onlyInCsv = collect([]);
		foreach ($this->csvRefunds() as $key => $item) {

			if (! $this->existsInDb($item)) {
				$onlyInCsv->push($item);
			}
		}
		// Keep results
		session()->put('refunds-only-csv',$onlyInCsv);
		return $onlyInCsv;
	}

	/**
	 * Get refund only in Both
	 */
	public function getInBoth()
	{
		return 	session()->get('refunds-in-both',collect([]));
	}

	/**
	 * Get refund only in Database
	 */
	public function getOnlyInDb()
	{
		return 	session()->get('refunds-only-db',collect([]));
	}

	/**
	 * Get refund only in CSV
	 */
	public function getOnlyInCsv()
	{
		return 	session()->get('refunds-only-csv',collect([]));
	}
	/**
	 * Check if item exists in csv
	 * @param  array  $item 
	 * @return array
	 */
	private function existsInCsv(array $item){
		$item = $this->csvRefunds()->where('adhersion_id',$item['adhersion_id'])->first();

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
		$item = $this->dbRefunds()->where('adhersion_id',$item['adhersion_id'])->first();

		if (empty($item)) {
			return false;
		}
		return $item;
	}
	/**
	 * Set refunds in the current sessions
	 * @param  $refunds 
	 */
	public function setDbRefunds($refunds)
	{
		session()->put('refunds-from-db',collect($refunds));
	}

	/**
	 * Set refunds from uploaded CSV
	 * @param  $refunds 
	 */
	public function setCsvRefunds($refunds)
	{
		session()->put('refunds-from-csv',collect($refunds));
	}

	/**
	 * Get Refunds for corrections
	 * @return  collection
	 */
	public function dbRefunds()
	{
		return session()->get('refunds-from-db',collect([]));
	}

	/**
	 * Get Csv Refunds for corrections
	 * @return  collection
	 */
	public function csvRefunds()
	{
		return session()->get('refunds-from-csv',collect([]));
	}

	/**
	 * Remove all items in the session
	 * @return void
	 */
	public function clear()
	{
		session()->forget('refunds-from-db');
		session()->forget('refunds-in-both');
		session()->forget('refunds-not-matching');
		session()->forget('refunds-only-db');
		session()->forget('refunds-only-csv');
		session()->forget('refunds-from-db');
		session()->forget('refunds-from-csv');
	}
}