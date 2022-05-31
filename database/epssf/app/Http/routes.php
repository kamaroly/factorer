<?php

use Ceb\Models\MonthlyFeeInventory;
use Ceb\Models\Refund;
use Ceb\Models\Setting;
use Ceb\Models\User;

Route::get('/marketing', function() {
    return view('welcome');
});

Route::group(['middleware' => 'sentry.auth'], function() {
    // All Application Middleware

	Route::get('/', ['as' => 'home','uses'=>'HomeController@index']);


	// NOTIFICATIONS
	Route::get('notifications',['as'=>'notificatons','uses'=>'MemberController@notificatons']);

	/** Members routes */
	Route::group(['prefix'=>'/members'], function(){
		Route::get('/search', 'MemberController@search');
		Route::get('/{memberId}/refund'					,['as'=>'members.refund' , 'uses'=>'MemberController@refund']);
		Route::get('/{memberId}/contribute'				,['as'=>'members.contribute' , 'uses'=>'MemberController@contribute']);
		Route::get('/{memberId}/transacts'				,['as'=>'members.transacts','uses'=>'MemberController@transacts']);
		Route::post('/{memberId}/completetransaction'	,['as'=>'members.completetransaction','uses'=>'MemberController@completeTransaction']);
		Route::get('/{memberId}/attornies'				,['as'=>'members.attornies','uses'=>'MemberController@attornies']);
		Route::get('/loanrecords/{memberId}'			,['as'=>'members.loanrecords','uses'=>'MemberController@loanRecords']);
		Route::get('/contributions/{memberId}'			,['as'=>'members.contributions','uses'=>'MemberController@contributions']);
		Route::get('/cautions/{memberid}'				,['as'=>'members.cautions.actives','uses'=>'MemberController@currentCautions']);
		Route::get('/cautioned/{memberid}'				,['as'=>'members.cautioned.actives','uses'=>'MemberController@currentCautionedByMe']);
			
	});
	Route::resource('members', 'MemberController');

	Route::group(['prefix' => 'closing'], function() {
	    Route::get('/','ContributionAndSavingsController@index')->name('closing.index');
	});


	/** Attornies routes */
	Route::resource('attornies','AttorneyController');

	/** Contribution routes */
	Route::group(['prefix'=>'/contributions'], function(){
		Route::post('complete','ContributionAndSavingsController@complete')->name('contributions.complete');
		Route::get('cancel'	  ,'ContributionAndSavingsController@cancel')->name('contributions.cancel');	
		Route::post('batch'	 ,'ContributionAndSavingsController@batch')->name('contributions.batch');
		Route::get('/export' ,'ContributionAndSavingsController@export')->name('contributions.export');

		// Annual interests routes
		Route::post('interests'	 ,'ContributionAndSavingsController@index')->name('contributions.interests');	

		Route::get('{adhersion_id}/remove','ContributionAndSavingsController@removeMember')->name('contributions.remove.member');
		Route::get('samplecsv','ContributionAndSavingsController@downloadSample')->name('contributions.sample.csv');
	});

	Route::resource('contributions', 'ContributionAndSavingsController');


	//Loan Routets
	Route::group(['prefix'=>'/loans'], function(){

		Route::get('/{id}', 'LoanController@selectMember')->where('id', '[0-9]+');
		Route::get('/cancel', ['as'						=>'loan.cancel', 'uses' => 'LoanController@cancel']);
		Route::get('/complete', ['as'					=> 'loan.complete', 'uses' => 'LoanController@complete']);
		Route::post('/complete', ['as'					=> 'loan.complete', 'uses' => 'LoanController@complete']);
		Route::get('/setcautionneur', 'LoanController@setCautionneur')->name('loan.add.cautionneur');
		Route::get('/pending/{loanId?}', ['as'			=> 'loan.pending', 'uses' => 'LoanController@getPending']);
		Route::get('/blocked/{loanId?}', ['as'			=> 'loan.blocked', 'uses' => 'LoanController@getBlocked']);
		Route::get('/process/{loanId}/{status}', ['as'	=> 'loan.process', 'uses' => 'LoanController@process']);
		Route::any('/unlblock', ['as'					=> 'loan.unblock.store', 'uses' =>'LoanController@unblock']);
		Route::get('/unblock/form/{loanId?}', ['as'		=> 'loan.unblock.form', 'uses' => 'LoanController@showUnblockingForm']);

		Route::get('/remove/cautionneur/{cautionneur}',
								  ['as'=> 'loan.remove.cautionneur',
							       'uses' => 'LoanController@removeCautionneur']
					)->where('cautionneur', '[A-Za-z0-9]+');

	});
	Route::resource('loans', 'LoanController');

		/** REGULARISATION ROUTES */
		Route::group(['prefix'=>'/regularisation'], function(){
			Route::get('/'				,['as' => 'regularisation.index', 'uses' => 'RegularisationController@index']);
			Route::get('/{id}'			,['as' => 'regularisation.setmember', 'uses' => 'RegularisationController@selectMember'])->where('id', '[0-9]+');
			Route::get('/setcautionneur',['as' => 'regularisation.add.cautionneur', 'uses' => 'RegularisationController@setCautionneur']);
			Route::get('/cancel'		,['as' => 'regularisation.cancel', 'uses' => 'RegularisationController@cancel']);
			Route::post('/complete'		,['as' => 'regularisation.complete', 'uses' => 'RegularisationController@complete']);
			Route::get('/remove/cautionneur/{cautionneur}',
								  ['as'=> 'regularisation.remove.cautionneur',
							       'uses' => 'RegularisationController@removeCautionneur']
					)->where('cautionneur', '[A-Za-z0-9]+');
		});

		/** Refunds routes */
		Route::group(['prefix'=>'/refunds'], function()
		{
			Route::post('/complete', ['as'	=> 'refunds.complete', 'uses' => 'RefundController@complete']);
			Route::get('/cancel', ['as'		=> 'refunds.cancel', 'uses' => 'RefundController@cancel']);
			Route::get('{adhersion_id}/remove'	,['as'=>'refunds.remove.member','uses'=>'RefundController@removeMember']);
			Route::any('batch',['as'=>'refunds.batch', 'uses'=>'RefundController@batch']);
			Route::get('/export',['as'=>'refunds.export','uses'=>'RefundController@export']);
		});
		Route::resource('refunds', 'RefundController');

	////////////////////////
	// CORRECTIONS ROUTES //
	////////////////////////
	Route::group(['prefix' => 'corrections','middleware'=>'sentry.admin'], function() {
		Route::group(['prefix' => 'loan'], function() {
		    Route::get('/', 'LoanCorrectionController@index')->name('corrections.loan.index');
		    Route::get('/show', 'LoanCorrectionController@show')->name('corrections.loan.show');
		    Route::post('/{transactionid}/adjust', 'LoanCorrectionController@update')->name('corrections.loan.update');
		});

		Route::group(['prefix' => 'refund'], function() {
		    Route::get('/', 'RefundCorrectionController@index')->name('corrections.refund.index');
		    Route::get('/show','RefundCorrectionController@show')->name('corrections.refund.show');
		    Route::put('/{id}/edit','RefundCorrectionController@edit')->name('corrections.refund.edit');
		    Route::get('/{id}/remove','RefundCorrectionController@remove')->name('corrections.refund.remove');
		    Route::post('/add','RefundCorrectionController@add')->name('corrections.refund.add');
		    Route::get('/update','RefundCorrectionController@show')->name('corrections.refund.update');

		    // Actions
		    Route::get('/complete','RefundCorrectionController@complete')->name('corrections.refund.complete');
		    Route::get('/destroy','RefundCorrectionController@destroy')->name('corrections.refund.destroy');
		    Route::get('/cancel','RefundCorrectionController@cancel')->name('corrections.refund.cancel');
		});
	    
	    // Contributions
	    Route::group(['prefix' => '/contributions'], function() {
		    Route::get('/', 'ContributionCorrectionController@index')->name('corrections.contributions.index');
		    Route::get('/show', 'ContributionCorrectionController@show')->name('corrections.contributions.show');
		    Route::post('/add', 'ContributionCorrectionController@add')->name('corrections.contributions.add');
		    Route::get('/complete', 'ContributionCorrectionController@complete')->name('corrections.contributions.complete');
		    Route::get('/destroy', 'ContributionCorrectionController@destroy')->name('corrections.contributions.destroy');
		    Route::get('/cancel', 'ContributionCorrectionController@cancel')->name('corrections.contributions.cancel');


		    Route::put('/{id}/edit','ContributionCorrectionController@edit')->name('corrections.contributions.edit');
		    Route::get('/{id}/remove','ContributionCorrectionController@remove')->name('corrections.contributions.remove');
	    });

	    // Approvals
	    Route::group(['prefix' => 'approvals'], function() {
	        Route::get('/','CorrectionApprovalController@index')->name('corrections.approvals');
	        Route::get('/{id}/show','CorrectionApprovalController@show')->name('corrections.approvals.show');
	        Route::get('/{id}/{status}','CorrectionApprovalController@changeStatus')->name('corrections.approvals.update');
	    });
	});


	    /////////////////////
	    // Reconsiliations //
	    /////////////////////
	    Route::group(['prefix' => '/reconciliations'], function() {
	        // Contributions
	        Route::group(['prefix' => 'contributions'], function() {
	            Route::get('/', 'ContributionReconciliationController@index')->name('reconciliations.contributions.index');
	            Route::get('/find/', 'ContributionReconciliationController@find')->name('reconciliations.contributions.find');
	            Route::post('/upload/', 'ContributionReconciliationController@upload')->name('reconciliations.contributions.upload');
	            Route::get('/{type}/download/', 'ContributionReconciliationController@download')->name('reconciliations.contributions.download');

	        });

	        // Refunds
	        Route::group(['prefix' => 'refunds'], function() {
	            Route::get('/', 'RefundReconciliationController@index')->name('reconciliations.refunds.index');
	            Route::get('/find/', 'RefundReconciliationController@find')->name('reconciliations.refunds.find');
	            Route::post('/upload/', 'RefundReconciliationController@upload')->name('reconciliations.refunds.upload');
	            Route::get('/{type}/download/', 'RefundReconciliationController@download')->name('reconciliations.refunds.download');

	        });

	        // Accounting
	       Route::group(['prefix' => '/account'], function() {
	            Route::get('/', 'BankReconciliationController@index')->name('reconciliations.account.index');
	            Route::get('/bank/', 'BankReconciliationController@index')->name('reconciliations.bank.index');
	            Route::post('/bank/reconcile', 'BankReconciliationController@reconcileBank')->name('reconciliations.bank.reconcile');
	            Route::get('/bank/reconciliations/download', 'BankReconciliationController@download')->name('reconciliations.bank.reconciliations.download');
	        });
	    });

		/** Accounting routes */
		Route::resource('accounting', 'AccountingController');

		/** Reporting routes */
		Route::group(['prefix'=>'/reports'], function(){	

				/** REPORT FILTERS */
				Route::get('/filter',['as'=>'reports.filter','uses'=>'ReportFilterController@filter']);
				Route::get('/', ['as' => 'reports.index', 'uses' => 'ReportController@index']);

				// ACOUNTING REPORTS 
				Route::group(['prefix'=>'/members'], function()
				{
					// CONTRACTS
					Route::get('contracts/saving/{memberId}/{export_excel?}',
							   ['as'=> 'reports.members.contracts.saving', 
							   'uses' => 'ReportController@contractSaving']);
					Route::get('contracts/loan/{loanId}/{export_excel?}', 
								['as'=> 'reports.members.contracts.loan', 'uses' => 'ReportController@contractLoan']);
					Route::get('contracts/ordinaryloan/{export_excel?}', 
								['as'=> 'reports.members.contracts.ordinaryloan', 'uses' => 'ReportController@ordinaryloan']);
					Route::get('contracts/socialloan/{export_excel?}', 
								['as'=> 'reports.members.contracts.socialloan', 'uses' => 'ReportController@socialloan']);
					
					// FILES
					Route::get('loanrecords/{startDate}/{endDate}/{export_excel?}/{memberId}'	,['as'=>'reports.members.loanrecords','uses'=>'ReportController@loanRecords']);
					Route::get('contributions/{startDate}/{endDate}/{export_excel?}/{memberId}','ReportController@contributions')->name('reports.members.contributions');

					// Countribution count in the curren Year
					Route::get('/contribution-count/{institution}/{export_excel?}', 'MemberReportController@countributionCount')->name('reports.members.contributions.count');

					// MONTHLY FEE INVENTORY
					Route::get('history/{startDate}/{endDate}/{export_excel?}/{anotherparam?}', 'ReportController@monthlyFeeInventory');
					
			        //Fin de dette entre deux date
					Route::get('octroye/{startDate}/{endDate}/{institution}/{export_excel?}'
						,'ReportController@octroye');

					Route::get('/hightloanthancontribution/{institution}/{export_excel?}/{anotherparam?}', 
								'MemberReportController@HigherLoanThanContribution'
							  )->name('reports.members.hightloanthancontribution');

					// All member details
					Route::get('/all-details/{institution}/{export_excel?}/{anotherparam?}', 
								'MemberReportController@allDetails'
							  )->name('reports.members.all-details');
				
				});

				// ACOUNTING REPORTS 
				Route::group(['prefix'=>'/accounting'], function()
				{
					Route::get('piece/{startDate}/{endDate}/{export_excel?}'	,['as' => 'reports.accounting.piece', 'uses' => 'ReportController@accountingPiece']);
					Route::get('ledger/{startDate}/{endDate}/{accountid}/{export_excel?}','ReportController@ledger')->name('reports.accounting.ledger');
					Route::get('bilan/{endDate}/{export_excel?}'	,['as'=>'reports.accounting.bilan','uses'=>'ReportController@bilan']);
					Route::get('journal/{startDate}/{endDate}/{export_excel?}'	,['as'=>'reports.accounting.journal','uses'=>'ReportController@journal']);
					Route::get('accounts/{export_excel?}'						,['as'=>'reports.accounting.accounts','uses'=>'ReportController@accountsList']);
					Route::get('/trial-balance/{endDate}/{export_excel?}', 'AccountingReportController@trialBalance')
							->name('reports.accounting.trial-balance');
					Route::get('cash-flow-statement/{startDate}/{endDate}/{export_excel?}', 'AccountingReportController@cashFlowStatement')->name('reports.accounting.cash-flow-statement');
				});

				// PIECES REPORTS 
				Route::group(['prefix'=>'/piece'], function()
				{
						Route::group(['prefix'=>'/disbursed'], function()
						{
							Route::get('saving/{transactionid}/{export_excel?}',['as'=>'piece.disbursed.saving','uses'=>'ReportController@pieceDisbursedSaving']);
							Route::get('accounting/{transactionid}/{export_excel?}',['as'=>'piece.disbursed.accounting','uses'=>'ReportController@pieceDisbursedAccounting']);
							Route::get('account/{startDate}/{endDate}/{account}/{export_excel?}',['as'=>'piece.disbursed.account',
																								  'uses'=>'ReportController@pieceDisbursedAccount']);
							Route::get('loan/{transactionid}/{export_excel?}',['as'=>'piece.disbursed.account','uses'=>'ReportController@pieceDisbursedLoan']);
							Route::get('refund/{transactionid}/{export_excel?}',['as'=>'piece.disbursed.refund','uses'=>'ReportController@pieceDisbursedRefund']);
					});
				});
				// CAUTIONS REPORTS 
				Route::group(['prefix'=>'/cautions'], function()
				{
					Route::get('/cautioned_me/{startDate}/{endDate}/{export_excel?}/{memberId}', ['as'=>'reports.cautions.me','uses'=>'ReportController@cautionedMe']);
					Route::get('/cautioned_by_me/{startDate}/{endDate}/{export_excel?}/{memberId}', ['as'=>'reports.loans','uses'=>'ReportController@cautionedByMe']);

				});
				// LOANS REPORTS 
				Route::group(['prefix'=>'/loans'], function()
				{
					Route::get('/balance/undefined/{endDate?}/{export_excel?}', ['as'=>'reports.loans.balance','uses'=>'ReportController@loansBalance']);

					Route::get('/balancedashbord/undefined/{export_excel?}', ['as'=>'reports.loans.balancedashboard','uses'=>'ReportController@loansBalancedashbord']);
					Route::get('/{startDate}/{endDate}/{status}/{export_excel?}', ['as'=>'reports.loans.status','uses'=>'ReportController@loans']);
				});

				// LOANS REPORTS 
				Route::group(['prefix'=>'/refunds'], function()
				{
					Route::get('monthly/{institution}/{export_excel?}', 'ReportController@monthlyRefund')
					      ->name('reports.refunds.monthly');
				 Route::get('monthlyContribution/{institution}/{export_excel?}', 'ReportController@monthlyContribution')
					      ->name('reports.refunds.monthlyContribution');

					Route::get('irreguralities/{institution?}/{export_excel?}','ReportController@refundIrregularities')
					     ->name('reports.refunds.irreguralities');

					Route::get('refundlatestpayment/{startDate}/{endDate}/{export_excel?}','ReportController@refundLatestPayment')
					      ->name('reports.refunds.refundlatestpayment');
					
					Route::get('/modified/{startDate}/{endDate}/{export_excel?}', 'ReportController@modifiedRefunds')
						  ->name('reports.modified.refunds');

				    Route::get('/at-risk/{institution?}/{export_excel?}','ReportController@loanAtRiskBy')->name('reports.refunds.at-risk');
				});

			    // LOANS REPORTS 
				Route::group(['prefix'=>'/savings'], function()
				{
					Route::get('level/{status?}/{endDate?}/{export_excel?}','ReportController@savingsLevel')->name('reports.savings.level');
					Route::get('allmember/{endDate?}/{export_excel?}','ReportController@savingsLevelMember')->name('reports.savings.allmember');

					//Report generated  via  dashboard 
					Route::get('leveldashboard/{status?}/{export_excel?}','ReportController@savingsLeveldashboard')->name('reports.savings.leveldashboard');
                    Route::get('allmemberdashboard/{institution?}/{export_excel?}','ReportController@savingsLevelMemberdashboard')->name('reports.savings.allmemberdashboard');
                    ///dashbord ordinary loan current  year

                     Route::get('dashordinarycurrentyear/{institution?}/{export_excel?}','ReportController@dashbordordinary')->name('reports.savings.dashordinarycurrentyear');
                      ///dashbord emergency loan current  year

                     Route::get('dashemergencycurrentyear/{institution?}/{export_excel?}','ReportController@dashbordemergency')->name('reports.savings.dashemergencycurrentyear');

                    ////
					Route::get('leftmember/{institution?}/{export_excel?}','ReportController@savingsleftmember')->name('reports.savings.leftmember');
					Route::get('newmember/{institution?}/{export_excel?}','ReportController@savingsnewmember')->name('reports.savings.newmember');
					Route::get('year_interest/{institution?}/{export_excel?}','ReportController@savingsyearinterest')->name('reports.savings.year_interest');
					Route::get('alldetails/{institution?}/{export_excel?}','ReportController@savingsAllDetails')->name('reports.savings.alldetails');
	                Route::get('institutionList/{institution?}/{export_excel?}','ReportController@institutionList')->name('reports.savings.institutionList');				
					Route::get('contribution-changes/{startDate}/{endDate}/{export_excel?}', 
							['as'=>'reports.savings.changes',
							'uses'=>'ReportController@contributionChanges']);

						// All guarantors

					Route::get('guarantors/{institution?}/{export_excel?}', 
								'ReportController@allguarantors'
							  )->name('reports.members.guarantors');
				});
				 // Contribution REPORTS 
				Route::group(['prefix'=>'/contributions'], function()
				{
					Route::get('notcontribuing/{institution?}/{export_excel?}', 'ReportController@notContribuing')
							->name('reports.contribution.not.contributing');
				});
		
	});

		/**LEAVES ROUTES*/
	Route::group(array('prefix' => 'leaves'), function() {
		    Route::get('/request', array('as' => 'leaves.request', 'uses' => 'LeaveController@create'));
		    Route::get('/show', array('as' => 'leaves.show', 'uses' => 'LeaveController@show'));
		    Route::get('/pending', array('as' => 'leaves.pending', 'uses' => 'LeaveController@index'));
		    Route::get('/approve/{leave}', array('as' => 'leaves.approve', 'uses' => 'LeaveController@approve'));
		    Route::get('reject/{leave}', array('as' => 'leaves.reject', 'uses' => 'LeaveController@reject'));
		    Route::get('status/{leave}', array('as' => 'leaves.status', 'uses' => 'LeaveController@status'));
	});
	Route::resource('leaves', 'LeaveController',['only' => ['index', 'store','create']]);

	/** SETTINGS ROUTE */
	Route::group(['prefix'=>'/settings'], function(){
		Route::resource('institution', 'InstitutionController');
		Route::resource('bank','BankController');
		Route::resource('accountingplan', 'AccountController');
	});

	/** Ajax routes */
	Route::group(['prefix' => 'ajax'], function () {
		Route::get('/loans', 'LoanController@ajaxFieldUpdate');
		Route::post('/loans/accounting', ['as' => 'ajax.accounting', 'uses' => 'LoanController@ajaxAccountingFeilds']);

		Route::get('/regularisation', 'RegularisationController@ajaxFieldUpdate');
		Route::post('/regularisation/accounting', ['as' => 'ajax.accounting', 'uses' => 'RegularisationController@ajaxAccountingFeilds']);

	});

	/** Files routes */
	Route::get('files', 'FileController@index');
	Route::get('files/get/{filename?}', [
		'as' => 'files.get', 'uses' => 'FileController@get']);
	Route::post('files/add', [
		'as' => 'files.add', 'uses' => 'FileController@add']);


	/** SENTINEL ROUTES */
	Route::get('settings/users', ['as' => 'ceb.settings.users.index', 'uses' => 'UserController@index']);

	/** UTILITY ROUTES */
	Route::get('/utility/backup',['as'=>'utility.backup','uses'=>'UtilityController@backup']);

	/**  ITEMS INVENTORY management group */
	Route::group(array('prefix'	=> '/items'), function() {
		Route::get('/', ['as'				=> 'items.index','uses'=>'ItemsController@index']);
		Route::any('/add', ['as'			=> 'items.add','uses'=>'ItemsController@add']);
		Route::any('/edit/{id}', ['as'		=> 'items.edit','uses'=>'ItemsController@edit'])->where('id', '[0-9]+');
		Route::get('/delete/{id}', ['as'	=> 'items.delete','uses'=>'ItemsController@delete'])->where('id', '[0-9]+');
	});

	/** DYNAMIC ASSETS ROUTES */
	Route::get('/js/loanform',['as'=>'assets.js.loanform','uses'=>function(){
		return response()->view('assets.js.loan_formjs')->header('Content-Type','application/javascript; charset=utf-8');
	}]);
	Route::get('/js/regularisationform',['as'=>'assets.js.regularisationform','uses'=>function(){
		return response()->view('assets.js.regularisation_formjs')->header('Content-Type','application/javascript; charset=utf-8');;
	}]);

	/** ROUTE FOR LOGS */
	Route::get('logs', ['as'=>'logs','middleware'=>'sentry.admin','uses'=>'\Rap2hpoutre\LaravelLogViewer\LogViewerController@index']);
});