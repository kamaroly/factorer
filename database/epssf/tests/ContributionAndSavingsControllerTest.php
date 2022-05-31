<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContributionAndSavingsControllerTest extends TestCase
{

    protected $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = $this->app->make('Ceb\Http\Controllers\ContributionAndSavingsController');
    }
    public function testUserCanSetRecurrence()
    {       
        // Update request
        Input::merge(['recurrence' => 'yearly']);
        $this->controller->setRecurrence();
        $this->assertSessionHas('contribution.recurrence','yearly');
    }

    public function testSetUserCanSetInterestYear()
    {
         Input::merge(['interest-year' => '2019']);
        $this->controller->setInterestYear();
        $this->assertSessionHas('interest-year','2019');
    }
}
