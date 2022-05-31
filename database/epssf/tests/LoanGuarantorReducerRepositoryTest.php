<?php

use Ceb\Models\User;
use Ceb\Repositories\Guarantors\LoanGuarantorReducerRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class LoanGuarantorReducerRepositoryTest extends TestCase
{
   private $memberId = 4881;

   /** @test */
   public function can_calculate_loan_amount_to_repay($value='')
   {
   		$member = User::find($this->memberId);
   		$loans   = $member->loans()->active()->get();
   		$amount =  432000;
   		// $reducer = (new LoanGuarantorReducerRepository($loans->first(),$amount));
   		loanGuarantorReducer($loans->first(),$amount);
   		// $results = $reducer->loanAmountToRepay($loans);

   		$this->assertEquals($member->loans->sum('loan_to_repay'),$results);
   }
}
