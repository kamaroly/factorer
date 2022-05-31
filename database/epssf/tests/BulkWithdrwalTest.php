<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BulkWithdrwalTest extends TestCase
{
	// 1. User can see closing module
    public function testUserCanSeeCloseModuleMenu()
    {
        $this->sentryUserBe('admin@admin.com');
        $this->visit(env('APP_URL'))
             ->click(trans('navigations.bulk_withdraw'))
             ->seePageIs(env('APP_URL').'/interests?contribution-type=BULK_WITHDRAW');
    }

    // 2. User can choose recurrence (Monthly/Yearly)
    // 3. Page title is updated to Year Closing / Bulk withdraw
    // 4. User can see his balance updated after this operations
    public function testUserCanBrowseCanFetchPeopleWithBulk()
    {
        $this->sentryUserBe('admin@admin.com');
        $this->visit(env('APP_URL'))
             ->click(trans('navigations.bulk_withdraw'))
             ->seePageIs(env('APP_URL').'/interests?contribution-type=BULK_WITHDRAW');
    }
}
