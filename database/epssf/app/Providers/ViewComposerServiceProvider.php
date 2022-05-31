<?php

namespace Ceb\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {

		$this->composerAccounting();
		$this->composerInstitutions();
		$this->composerMembers();
		$this->composerMonthYear();
		$this->composerJournals();
		$this->composerLoanTypes();
		$this->composerMemberTransactions();
		$this->composerBanks();
		$this->composerDashboard();
		$this->composerAccountNature();
		$this->composerLanguages();
		$this->composerUser();
		$this->composerSetting();
		$this->composerSocialLoanReasons();
		$this->composerRegularisactions();
		$this->composerLoanStatus();
		$this->composerLoanRate();
		$this->compserRefundTypes();
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}

	/**
	 * Accounts view composers
	 * @return mixed
	 */
	private function composerAccounting() {
		$views = [
			'refunds.form',
			'accounting.form',
			'accounting.debit',
			'accounting.credit',
			'members.transactions',
			'reports.filters.date_filter',
			'corrections.loan.accounting',
			'contributionsandsavings.form',
			'reconciliations.banks.index',
		];

		view()->composer($views, 'Ceb\ViewComposers\AccountViewComposer');
	}
	/**
	 * Institution view Composers
	 * @return void
	 */
	private function composerInstitutions() {
		
		$views = [
			'members.form',
			'refunds.form',
			'contributionsandsavings.list',
			'reports.filters.date_filter',
		];

		view()->composer($views, 'Ceb\ViewComposers\InstitutionsViewComposer');
	}
	/**
	 * Member View Composer
	 *
	 * @return void
	 */
	private function composerMembers() {
		$views = [
			'partials.nav_left',
			'partials.dashboard',
		];

		view()->composer($views, 'Ceb\ViewComposers\MembersViewComposer');
	}

	/**
	 * Month Year view composer
	 * @return void
	 */
	private function composerMonthYear() {
		$views = [
			'refunds.form',
		];

		view()->composer($views, 'Ceb\ViewComposers\MonthYearViewComposer');

	}
	/**
	 * Journals view composer
	 * @return void
	 */
	private function composerJournals() {

		$views = [
			'accounting.journal',
		];

		view()->composer($views, '\Ceb\ViewComposers\JournalViewComposer');
	}

	/**
	 * LoanTypes view composer
	 * @return void 
	 */
	private function composerLoanTypes()
	{
		$views = [
			'regularisation.form',
			'loansandrepayments.ordinary_loan_form',
			'regularisation.ordinary_loan_form',
			'reports.filters.date_filter',
			'regularisation.loanTypes',
		];
		view()->composer($views, '\Ceb\ViewComposers\LoanTypeViewComposer');
	}

	private function composerMemberTransactions()
	{
		$views = [
			'members.transactions',	
		];

		view()->composer($views,'\Ceb\ViewComposers\MemberTransactionsViewComposer');
	}

	private function composerBanks()
	{
		$views = [
			'accounting.journal',
			'members.transactions',
			'regularisation.index',
			'loansandrepayments.ordinary_loan_form',
			'loansandrepayments.special_loan_form',
			'loansandrepayments.unblock_form',
		];

		view()->composer($views,'\Ceb\ViewComposers\BanksViewComposer');
	}

	private function composerDashboard()
	{
		$views = [
			'partials.dashboard'
		];
		view()->composer($views,'\Ceb\ViewComposers\DashboardViewComposer');
	}

	private function composerAccountNature()
	{
		$views = [
			'settings.accountingplan.form',
		];
		view()->composer($views,'\Ceb\ViewComposers\AcccountNatureViewComposer');
	}

	private function composerLanguages()
	{
	    $views = [
			'sentinel.users.edit',
		];
		view()->composer($views,'\Ceb\ViewComposers\LanguagesViewComposer');
	}

	private function composerUser()
	{
		
	    $views = [
			'*',
		];
		view()->composer($views,'\Ceb\ViewComposers\UserViewComposer');
	}

	private function composerSetting()
	{
		$views = [
			'*',
		];
		view()->composer($views,'\Ceb\ViewComposers\SettingViewComposer');
	}

	private function composerSocialLoanReasons()
	{
		$views = [
			'loansandrepayments.ordinary_loan_form',
		];
		view()->composer($views,'\Ceb\ViewComposers\SocialLoanMotivesViewComposer');
	}

	private function composerRegularisactions()
	{
		
		$views = [
			'regularisation.ordinary_loan_form',
		];
		view()->composer($views,'\Ceb\ViewComposers\RegularisactionViewComposer');
	}

	private function composerLoanStatus()
	{
		
		$views = [
			'reports.filters.date_filter',
		];
		view()->composer($views,'\Ceb\ViewComposers\LoanStatusViewComposer');
	}

	private function composerLoanRate()
	{
		
		$views = [
			'assets.js.loan_formjs',
			'assets.js.regularisation_formjs',

		];
		view()->composer($views,'\Ceb\ViewComposers\LoanRateViewComposer');
	}

	private function compserRefundTypes()
	{
		$views = [
		     'refunds.form',

		];
		view()->composer($views,'\Ceb\ViewComposers\RefundTypesViewComposer');
	}
	
}
