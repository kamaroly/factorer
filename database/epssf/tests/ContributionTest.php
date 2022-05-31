<?php

use Ceb\Models\Contribution;
use Ceb\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContributionTest extends TestCase
{
    private $contribution;
    
    public function setUp()
    {
        parent::setUp();
        $this->contribution = $this->app->make('Ceb\Models\Contribution');
    }

    public function testSavingLevelsMembers()
    {
		$members      = collect($this->contribution->savingLevel())->count();
		// Members in the system
		$users 		= User::where('institution_id','>', 0)->count();
        $this->assertEquals($members,$users);
    }

    public function testSavingLevelsActive()
    {
		$members      = collect($this->contribution->savingLevel('actif'))->count();
		// Members in the system
		$users 		= User::where('status','actif')->where('institution_id','>', 0)->count();
        $this->assertEquals($members,$users);
    }
    public function testSavingLevelsInActive()
    {
		$members      = collect($this->contribution->savingLevel('inactif'))->count();
		// Members in the system
		$users 		= User::where('status','inactif')->where('institution_id','>', 0)->count();
        $this->assertEquals($members,$users);
    }

    public function testGetReceivedInterestFor()
    {
        $results      = $this->contribution->ReceivedInterestFor('2013')->count();
        // Members in the system
        $expected      = Contribution::whereWording('ANNUAL_INTEREST_2013')->count();
        $this->assertEquals($results,$expected);
    }

    public function testCanReceivedInterestFor()
    {
        $year         = '2013';
        $results      = $this->contribution->CanReceivedInterestFor($year)->count();
        // Members in the system
        $expected      = Contribution::receivedInterestFor($year)->count();
        $expected      = $expected - Contribution::withdrewInterestFor($year)->count();
        $this->assertEquals($results,$expected);
    }
}
