<?php
use Ceb\factories\BankReconciliationFactory;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankReconciliationFactoryTest extends TestCase
{
	protected $accounts = [59,11,6,10];

	protected $amounts  = [1000,2000,3000,4000];
	
	protected $factory;

    public function setUp()
    {
    	parent::setUp();

    	$this->accounts = collect($this->accounts);
    	$this->amounts = collect($this->amounts);
    }
    public function test_mergeBankData()
    {
        $factory = $this->app->make('Ceb\Factories\BankReconciliationFactory');

        $this->assertTrue(!$factory->mergeBankData($this->accounts,$this->amounts)->isEmpty());
    }
    public function test_matchAccounts()
    {
		$factory        = $this->app->make('Ceb\Factories\BankReconciliationFactory');
		$accountBalance = $factory->accountsBalance($this->accounts);
		$bankData       = $factory->mergeBankData($this->accounts,$this->amounts);

        $this->assertEquals($factory->matchAccounts($accountBalance,$bankData)->count(),$accountBalance->count());
    }

    public function test_reconcile()
    {
        $factory = $this->app->make('Ceb\Factories\BankReconciliationFactory');
        $this->assertEquals(class_basename($factory->reconcile($this->accounts,$this->amounts)),'Collection');
    }
}
