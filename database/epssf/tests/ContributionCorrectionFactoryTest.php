<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContributionCorrectionFactoryTest extends TestCase
{
   	private $factory;

   	public function setUp()
   	{
   		parent::setUp();
   		$this->factory = $this->app->make(Ceb\Factories\ContributionCorrectionFactory::class);
   	}

   	public function test_setcontribution()
   	{
   		$this->factory->setContributions(collect([['one'],['two'],['three']]));

   		$this->assertEquals($this->factory->contributions()->count(),0);
   	}
}
