<?php

use Illuminate\Database\Seeder;

class DefaultAccountsSeeder extends Seeder
{
/**
* Run the database seeds.
*
* @return void
*/
public function run()
{
	
$accounts =[
			0				=>[
			 "module"		=> "contribution"
			,"function"		=> "batch_contribution"
			,"account_id"	=> "5610"
			,"entitled"		=> "BK Butare"
			,"type"			=> "debit"
			],
			1				=>[
			"module"		=> "contribution"
			,"function"		=> "batch_contribution"
			,"account_id"	=> "4410"
			,"entitled"		=> "Epargne des membres"
			,"type"			=> "credit"
			],
			2				=>[
			"module"		=>"loans"
			,"function"		=> "ordinary_loan"
			,"account_id"	=> "5611"
			,"entitled"		=> "BCR"
			,"type"			=> "credit"
			],
			3				=>[
			"module"		=>"loans"
			,"function"		=> "ordinary_loan"
			,"account_id"	=> "4110"
			,"entitled"		=> "Prets aux membres"
			,"type"			=> "debit"
			],
			4				=>[
			"module"		=>"loans"
			,"function"		=> "ordinary_loan"
			,"account_id"	=> "7710"
			,"entitled"		=> "Interets sur P.0"
			,"type"			=> "credit"
			],
			5				=>[
			"module"		=>"loans"
			,"function"		=> "ordinary_loan"
			,"account_id"	=> "7410"
			,"entitled"		=> "Profits divers"
			,"type"			=> "Credit"
			],
			6				=>[
			"module"		=>"loans"
			,"function"		=> "special_loan"
			,"account_id"	=> "5611"
			,"entitled"		=> "BCR"
			,"type"			=> "credit"
			],
			7				=>[
			"module"		=>"loans"
			,"function"		=> "special_loan"
			,"account_id"	=> "4110"
			,"entitled"		=> "Prets aux membres"
			,"type"			=> "debit"
			],
			8				=>[
			"module"		=>"loans"
			,"function"		=> "special_loan"
			,"account_id"	=> "7711"
			,"entitled"		=> "Interets sur PS"
			,"type"			=> "credit"
			],
			9				=>[
			"module"		=>"loans"
			,"function"		=> "social_loan"
			,"account_id"	=> "5611"
			,"entitled"		=> "BCR"
			,"type"			=> "credit"
			],
			10				=>[
			"module"		=>"loans"
			,"function"		=> "social_loan"
			,"account_id"	=> "4110"
			,"entitled"		=> "Prets aux membres"
			,"type"			=> "debit"
			],
			11				=>[
			"module"		=>"loans"
			,"function"		=> "social_loan"
			,"account_id"	=> "7410"
			,"entitled"		=> "Profits divers"
			,"type"			=> "credit"
			],
			12				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_installments"
			,"account_id"	=> "5611"
			,"entitled"		=> "BCR"
			,"type"			=> "debit"
			],
			13				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_installments"
			,"account_id"	=> "7711"
			,"entitled"		=> "Interets sur PS"
			,"type"			=> "credit"
			],
			14				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount"
			,"account_id"	=> "5611"
			,"entitled"		=> "BCR"
			,"type"			=> "credit"
			],
			15				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount"
			,"account_id"	=> "4110"
			,"entitled"		=> "Prets aux membres"
			,"type"			=> "debit"
			],
			16				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount"
			,"account_id"	=> "7711"
			,"entitled"		=> "Interets sur PS"
			,"type"			=> "credit"
			],
			17				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount"
			,"account_id"	=> "7410"
			,"entitled"		=> "Profits divers"
			,"type"			=> "credit"
			],
			18				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount_installments"
			,"account_id"	=> "5611"
			,"entitled"		=> "BCR"
			,"type"			=> "credit"
			],
			19				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount_installments"
			,"account_id"	=> "4110"
			,"entitled"		=> "Prets aux membres"
			,"type"			=> "credit"
			],
			20				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount_installments"
			,"account_id"	=> "7711"
			,"entitled"		=> "Interets sur PS"
			,"type"			=> "debit"
			],
			21				=>[
			"module"		=>"loans"
			,"function"		=> "regularisation_amount_installments"
			,"account_id"	=> "7410"
			,"entitled"		=> "Profits divers"
			,"type"			=> "credit"
			],
			22				=>[
			"module"		=> "Saisi Intérêts annuels"
			,"function"		=> "regularisation_amount_installments"
			,"account_id"	=> "4410"
			,"entitled"		=> "Epargne des membres"
			,"type"			=> "credit"
			],
			23				=>[
			"module"		=> "Saisi Intérêts annuels"
			,"function"		=> "regularisation_amount_installments"
			,"account_id"	=> "8700"
			,"entitled"		=> "Resultats"
			,"type"			=> "debit"
			],
			24				=>[
			"module"		=> "member_transactions"
			,"function"		=> "member_transaction_withdraw"
			,"account_id"	=> "5610"
			,"entitled"		=> "BK Butare"
			,"type"			=> "credit"
			],
			25				=>[
			"module"		=> "member_transactions"
			,"function"		=> "member_transaction_withdraw"
			,"account_id"	=> "7410"
			,"entitled"		=> "Profits divers"
			,"type"			=> "credit"
			],
			26				=>[
			"module"		=> "member_transactions"
			,"function"		=> "member_transaction_withdraw"
			,"account_id"	=> "4410"
			,"entitled"		=> "Epargne des membres"
			,"type"			=> "debit"
			],
			27				=>[
			"module"		=> "member_transactions"
			,"function"		=> "member_transaction_saving"
			,"account_id"	=> "4410"
			,"entitled"		=> "Epargne des membres"
			,"type"			=> "credit"
			],
			28				=>[
			"module"		=> "member_transactions"
			,"function"		=> "member_transaction_saving"
			,"account_id"	=> "8700"
			,"entitled"		=> "Resultats"
			,"type"			=> "debit"
			],
			29				=>[
			"module"		=> "member_transactions"
			,"function"		=> "member_transaction_saving"
			,"account_id"	=> "6702"
			,"entitled"		=> "Interets retournes"
			,"type"			=> "debit"
			],
			30				=>[
			"module"		=>"member_transactions"
			,"function"		=>"member_transaction_saving"
			,"account_id"	=>"4110 "
			,"entitled"		=>"Prets aux membres"
			,"type"			=>"debit"
			],
			31				=>[
			"module"		=>"refunds"
			,"function"		=>"refunds_individual"
			,"account_id"	=>"4110"
			,"entitled"		=>"Pret aux membre"
			,"type"			=>"credit"
			],
			32				=>[
			"module"		=>"refunds"
			,"function"		=>"refunds_individual"
			,"account_id"	=>"4410"
			,"entitled"		=>"Epargne des membres"
			,"type"			=>"debit"
			],
			33				=>[
			"module"		=>"refunds"
			,"function"		=>"refunds_batch"
			,"account_id"	=>"5610"
			,"entitled"		=>"BK Butare"
			,"type"			=>"debit"
			],
			34				=>[
			"module"		=>"refunds"
			,"function"		=>"refunds_batch"
			,"account_id"	=>"4110"
			,"entitled"		=>"Prets aux membres"
			,"type"			=>"credit"
			]
		];

	DB::table('default_accounts')->truncate();

DB::table('default_accounts')->insert($accounts);
}
}
