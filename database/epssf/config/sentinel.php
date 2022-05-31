<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Registration
	|--------------------------------------------------------------------------
	|
	| By default, registration is enabled.  To turn off registration, change this
	| value to false.
	|
	 */

	'registration' => true,

	/*
	|--------------------------------------------------------------------------
	| Activation
	|--------------------------------------------------------------------------
	|
	| By default, new accounts must be activated via email.  Setting this to
	| false will allow users to login immediately after signing up.
	|
	 */

	'require_activation' => false,

	/*
	|--------------------------------------------------------------------------
	| Allow Usernames
	|--------------------------------------------------------------------------
	|
	| By default, Sentry (and Sentinel) will only let a user log in using their
	| email address.  By setting 'allow_usernames' to true, a user can enter either
	| their username or their email address as a login credential.
	|
	 */

	'allow_usernames' => true,

	/*
	|--------------------------------------------------------------------------
	| Default User Groups
	|--------------------------------------------------------------------------
	|
	| When a new user is created, they will automatically be added to the
	| groups in this array.
	|
	 */

	'default_user_groups' => ['Users'],

	/*
	|--------------------------------------------------------------------------
	| Default Group Permissions
	|--------------------------------------------------------------------------
	|
	| When a new user is created, they will automatically be added to the
	| groups in this array.
	|
	 */

	'default_permissions' => [
		 'admin',
	     'users',
	     'ceb.view.own.profile',
		 // ACCOUNTING
		'account.list',
		'account.create',
		'account.edit',
		'account.delete',
		'accounting.view',
		'accounting.posting',
		'attornies.add',

		
		// CONTRIBUTION
		'contribution.index',
		'contribution.add',
		'contribution.view',
		'contribution.update',
		'contribution.complete',
		'contribution.remove.member',
		'contribution.cancel',
		'contribution.batch.contribution',
		'contribution.download.sample',
		'contribution.set.month',
		'contribution.set.credit.account',
		'contribution.set.debit.account',
		'contribution.set.by.institution',
		'contribution.remove.contribution.with.differences',
		'contribution.export.contribution.with.differences',
		'contribution.export.contribution.with.errors',
		 // GENERAL FILES
		'files.view',
		'files.add',
		 // INSTITUTIONS
		'institutions.view',
		'institutions.edit',
		'institutions.create',
		'institutions.delete',
		//LEAVE AND ITEMS
		'leaves.view',
		'leaves.approve',
		'leaves.reject',
		'leaves.view.my.leaves',
		'leaves.request.leaves',
		'leaves.view.leave.status',
		// ITEMS
		'items.index',
		'items.add',
		'items.edit',
		'items.edit',
		'items.view',
		// REFUNDS
		'refund.index',
		'refund.update',
		'refund.complete',
		'refund.cancel',
		'refund.set.institution',
		'refund.set.debit.account',
		'refund.set.credit.account',
		'refund.set.month',
		'refund.remove.member',
		'refund.batch',
		'refund.export.refunds.with.differences',
		'refund.export.refund.with.errors',
		'refund.remove.refunds.with.differences',
		// LOANS
		'loan.index',
		'loan.add.member.to.loan.form',
		'loan.complete.loan.request',
		'loan.cancel.loan.request',
		'loan.set.loan.cautionneur',
		'loan.remove.loan.cautionneur',
		'loan.check.loan.status',
		'loan.can.approve.loan',
		'loan.can.unblock.loan',
		// REGULARISATION
		'regularisation.view',
		'regularisation.index',
		'regularisation.installments',
		'regularisation.amount',
		'regularisation.amount.installments',
		// MEMBERS
		'member.list',
	    'member.view',
	    'member.edit',
	    'member.transact',
	    'member.create',
	    'member.delete',
		'member.view.current.i.cautioned',
	    'member.view.current.cautioned.by.me',
	    'member.user_vue',
		// REPORTS
		'reports.index',
		'reports.contract.saving',
		'reports.contract.loan',
		'reports.member',
		'reports.accounting.piece',
		'reports.ledger',
        // FILE
		'reports.bilan',
		'reports.journal',
		'reports.accounts.list',
		'reports.loans.records',
		'reports.contributions',
		'reports.loans.status',
		'reports.loan.balance',

		'reports.modified.refunds',
		'reports.monthly.refound',
		'reports.savings.level',
		'reports.savings.changes',
		'reports.savings.irreguralities',
		'reports.refunds.irreguralities',
		'reports.members_who_cautionned_me',
		'reports.refunds.refund_latest_payment',

		'reports.members_cautionned_by_me',
		'reports.piece.debourse.saving',
		'reports.piece.debourse.account',
		'reports.piece.debourse.accounting',
		'reports.piece.debourse.loan',
		'reports.piece.debourse.refund',

		'utility.can.do.database.backup',
		'utility.can.do.check.member',
		],

	/*
	|--------------------------------------------------------------------------
	| Custom User Fields
	|--------------------------------------------------------------------------
	|
	| If you want to add additional fields to your user model you can specify
	| their validation needs here.  You must update your db tables and add
	| the fields to your 'create' and 'edit' views before this will work.
	|
	 */

	'additional_user_fields' => [
		'first_name' => 'alpha_spaces',
		'last_name' => 'alpha_spaces',
		'language'	=> 'alpha',
	],

	/*
	|--------------------------------------------------------------------------
	| E-Mail Subject Lines
	|--------------------------------------------------------------------------
	|
	| When using the "Eloquent" authentication driver, we need to know which
	| Eloquent model should be used to retrieve your users. Of course, it
	| is often just the "User" model but you may use whatever you like.
	|
	 */

	'subjects' => [
		'welcome' => 'Account Registration Confirmation',
		'reset_password' => 'Password Reset Confirmation',
	],

	/*
	|--------------------------------------------------------------------------
	| Default Routing
	|--------------------------------------------------------------------------
	|
	| Sentinel provides default routes for its sessions, users and groups.
	| You can use them as is, or you can disable them entirely.
	|
	 */

	'routes_enabled' => true,

	/*
	|--------------------------------------------------------------------------
	| URL Redirection for Method Completion
	|--------------------------------------------------------------------------
	|
	| Upon completion of their tasks, controller methods will look-up their
	| return destination here. You can specify a route, action or URL.
	| If no action is specified a JSON response will be returned.
	|
	 */

	'routing' => [
		'session_store' => ['route' => 'home'],
		'session_destroy' => ['action' => '\\Sentinel\Controllers\SessionController@create'],
		'registration_complete' => ['route' => 'home'],
		'registration_activated' => ['route' => 'home'],
		'registration_resend' => ['route' => 'home'],
		'registration_reset_triggered' => ['route' => 'home'],
		'registration_reset_invalid' => ['route' => 'home'],
		'registration_reset_complete' => ['route' => 'home'],
		'users_invalid' => ['route' => 'home'],
		'users_store' => ['route' => 'sentinel.users.index'],
		'users_update' => ['route' => 'sentinel.users.show', 'parameters' => ['user' => 'hash']],
		'users_destroy' => ['route' => 'sentinel.users.index'],
		'users_change_password' => ['route' => 'sentinel.users.show', 'parameters' => ['user' => 'hash']],
		'users_change_memberships' => ['route' => 'sentinel.users.show', 'parameters' => ['user' => 'hash']],
		'users_suspend' => ['route' => 'sentinel.users.index'],
		'users_unsuspend' => ['route' => 'sentinel.users.index'],
		'users_ban' => ['route' => 'sentinel.users.index'],
		'users_unban' => ['route' => 'sentinel.users.index'],
		'groups_store' => ['route' => 'sentinel.groups.index'],
		'groups_update' => ['route' => 'sentinel.groups.index'],
		'groups_destroy' => ['route' => 'sentinel.groups.index'],
		'profile_change_password' => ['route' => 'sentinel.profile.show'],
		'profile_update' => ['route' => 'sentinel.profile.show'],
	],

	/*
	|--------------------------------------------------------------------------
	| Enable HTML Views
	|--------------------------------------------------------------------------
	|
	| There are situations in which you may not want to display any views
	| when interacting with Sentinel.  To return JSON instead of HTML,
	| turn this setting off. This cannot be done selectively.
	|
	 */

	'views_enabled' => true,

	/*
	|--------------------------------------------------------------------------
	| Master Layout
	|--------------------------------------------------------------------------
	|
	| By default Sentinel views will extend their own master layout. However,
	| you can specify a custom master layout view to use instead.
	|
	 */

	'layout' => 'layouts.default',

];