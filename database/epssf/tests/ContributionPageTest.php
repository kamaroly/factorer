<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContributionPageTest extends TestCase
{
	public function testUserCanSeeAnnualInterestLinkOnDashboard()
	{
	    $this->sentryUserBe('admin@admin.com');

        $this->visit(env('APP_URL').'/')
            ->click(trans('navigations.annual_interest'))
            ->seePageIs(route('contributions.interests').'?contribution-type=ANNUAL_INTEREST');
	}

    public function testUserCanSelectContributionType()
    {
        $this->sentryUserBe('admin@admin.com');
        $this->visit(env('APP_URL').'/')
            ->click(trans('navigations.annual_interest'))
            ->see(trans('contribution.year_of_interest'))
            ->select('2019','interest_year');
    }

    public function testCanUploadMembersForBulkWithdraw()
    {
        // $this->withoutMiddleware();
        $this->sentryUserBe('admin@admin.com');

        $this->visit(env('APP_URL').'/')
            ->click(trans('navigations.annual_interest'))
            ->see(trans('contribution.year_of_interest'))
            ->attach('/stubs/bulk-withdraw-sample.csv', 'file')
            ->press('Upload');

       // // Check if the members has been set
       // $factory = $this->app->make('Ceb\Factories\ContributionFactory');
       // // Make this bulk withdraw
       // $factory->setContributionType('BULK_WITHDRAW');

       // $this->assertEquals($factory->getConstributions()->count(),3);
    }
}
