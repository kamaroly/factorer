<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCanFilterMemberStatus()
    {
    	$this->sentryUserBe('admin@admin.com');

        $this->visit(env('APP_URL').'/reports')
            ->click('report-savings-contributions')
            ->seePageIs(env('APP_URL').'/reports/filter?reporturl=reports%2Fsavings%2Flevel&show_exports=true&show_member_status=true')
            ->see('Select Member Status')
            ->select('active', 'member_status');
    }

    public function testReportHasStatusColumnOnActive()
    {
        $this->sentryUserBe('admin@admin.com');
        $this->visit(env('APP_URL').'/reports/savings/level/Actif/0')
             ->see('Status')
             ->see('actif');
    }

    public function testReportHasStatusColumnOnInactive()
    {
        $this->sentryUserBe('admin@admin.com');
        $this->visit(env('APP_URL').'/reports/savings/level/inactif/0')
             ->see('Status')
             ->see('inactif');
    }
}
