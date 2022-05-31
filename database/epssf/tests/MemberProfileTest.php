<?php

use Ceb\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberProfileTest extends TestCase
{

    public function testUserCanSeeMemberStatusOnProfile()
    {
    		$member = User::orderBy('adhersion_id','DESC')->first();

        	$this->sentryUserBe('admin@admin.com');
	        $this->visit(env('APP_URL').'/members/'.$member->id)
	        	 ->see(trans('member.status'))
	        	 ->select('inactif','status')
	        	 ->select('actif','status');
    }

    public function testMemberCanBeActivated()
    {
    		$member = User::orderBy('adhersion_id','DESC')->first();

        	$this->sentryUserBe('admin@admin.com');
	        $this->visit(env('APP_URL').'/members/'.$member->id)
	        	 ->see(trans('member.status'))
	        	 ->select('inactif','status')
	        	 ->press('save-member');
	        // Refresh the database and see what 
	        $member = $member->fresh();
    		$this->assertEquals('inactif',$member->status);
    }

    public function testMemberCanBeInActivated()
    {
    		$member = User::orderBy('adhersion_id','DESC')->first();

        	$this->sentryUserBe('admin@admin.com');
	        $this->visit(env('APP_URL').'/members/'.$member->id)
	        	 ->see(trans('member.status'))
	        	 ->select('actif','status')
	        	 ->press('save-member');
	        // Refresh the database and see what 
	        $member = $member->fresh();
    		$this->assertEquals('actif',$member->status);
    }

    public function testMemberCanCanBankAccount()
    {
    		$member = User::orderBy('adhersion_id','DESC')->first();

        	$this->sentryUserBe('admin@admin.com');
	        $this->visit(env('APP_URL').'/members/'.$member->id)
	        	 ->see(trans('member.bank-account'))
	        	 ->type('00040-00293300-05', 'bank_account')
	        	 ->press('save-member');
	        // Refresh the database and see what 
	        $member = $member->fresh();
    		$this->assertEquals('00040-00293300-05',$member->bank_account);
    }
}
