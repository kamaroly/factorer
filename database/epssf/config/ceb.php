<?php 
return [
	// Banks in CEB
	'banks' => [
				'BK'                           => 'BANK OF KIGALI',
				
				
	],

	/*
	|--------------------------------------------------------------------------
	| CEB  accounts that are not allowed for some of the module
	|--------------------------------------------------------------------------
	 */
	'prohibiten_accounts' => [
			// Prohibidden accounts for the accounting
			// activities 
	        '4110 - Prets aux membres',	
		    '4410 - Epargne des membres',
			'4112 - Prêt aux membres inactifs',
			'4411 - Epargne des membres inactif',	
	
			'accounting' => [		
						    
			],
	],

	/**
	 * Cash Flow  statement operating accounts
	 */
	'operating_activities_accounts'   => ['8750','4110','4111','4210','4610','4612','4613','4614','4615',
										  '4616','4617','4618','4619','4620','4700','4310','4311','4312',
										  '4410','4411','4412','4611','4701','4800','8700',],
	
	/**
	 * Cash Flow  statement investiment accounts
	 */
	'investiment_activities_accounts' => ['2210','2211','2212',],

	/**
	 * Finance activities accounts
	 */
	'financing_activities_accounts'   => ['1000','1100',],
];