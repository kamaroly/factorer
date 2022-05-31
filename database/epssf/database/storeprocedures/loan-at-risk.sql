DROP PROCEDURE IF EXISTS SP_LOAN_AT_RISK;
DELIMITER ;;
CREATE PROCEDURE SP_LOAN_AT_RISK()
BEGIN

-- 1. Get user / members details with institutions 
-- * Name
-- * Adhersion Number
-- * Instution 
DROP TABLE IF EXISTS temp_members;
CREATE TEMPORARY TABLE temp_members(
	SELECT 
	  CASE 
	  	WHEN first_name IS NULL THEN last_name
	  	WHEN last_name IS NULL THEN first_name
	  	ELSE CONCAT(first_name,' ',last_name) 
	  	END AS name,
		adhersion_id,
		institutions.name institution
	FROM users, institutions 
	WHERE users.institution_id = institutions.id
);

-- 2. Get Refund details by adhersion number 
-- * Total refund sum(amount)
-- * Maximum refund date / Last rendud at
DROP TABLE IF EXISTS temp_refunds;
CREATE TEMPORARY TABLE temp_refunds(
	SELECT adhersion_id,
		   contract_number,
		   sum(amount) as refunds, 
		   max(created_at) last_refunded_at
    FROM refunds 
    GROUP BY adhersion_id,contract_number
);

-- 3. Get Loan information 
-- * contract_number
-- * Loan Type / Operation Type
-- * Loan Date
-- * Loan To loan 
-- * Trancheee 
-- * Balance 
DROP TABLE IF EXISTS temp_loans;
CREATE TEMPORARY TABLE temp_loans(
	SELECT 
		   adhersion_id,
		   loan_contract,
		   operation_type,
		   date(created_at) loan_date,	
		   loan_to_repay,
		   monthly_fees
   FROM loans 
   WHERE `status` = 'approved'
);

---remove all  emergency loan  before migration of  cebms  upgrade

DROP TABLE IF EXISTS temp_loans_without_emergency_before;
CREATE TEMPORARY TABLE temp_loans_without_emergency_before(
         SELECT 
		   adhersion_id,
		   loan_contract,
		   operation_type,
		   loan_date,	
		   loan_to_repay,
		   monthly_fees
   FROM temp_loans
   WHERE  loan_contract not in (select distinct loan_contract from loans where operation_type ='emergency_loan'
    and created_at<='2020-05-22 23:59:59'));
   

--  PUT ALL THINGS TOGETHER
SELECT 
	temp_members.name,
	temp_members.adhersion_id,
	temp_members.institution,
	temp_loans_without_emergency_before.loan_contract,
	temp_loans_without_emergency_before.operation_type,
	temp_loans_without_emergency_before.loan_date,
	temp_loans_without_emergency_before.loan_to_repay,
	temp_loans_without_emergency_before.monthly_fees,
	temp_refunds.refunds,
	temp_refunds.last_refunded_at,
	temp_loans_without_emergency_before.loan_to_repay - temp_refunds.refunds AS balance
FROM temp_members 
LEFT JOIN temp_loans_without_emergency_before ON temp_members.adhersion_id = temp_loans_without_emergency_before.adhersion_id
LEFT JOIN temp_refunds ON temp_loans_without_emergency_before.loan_contract = temp_refunds.contract_number
WHERE (temp_loans_without_emergency_before.loan_to_repay - temp_refunds.refunds) > 0 AND last_refunded_at <= date(now()-interval 3 month)
ORDER BY loan_date;
END;
;;
-- FOR TESTING PURPOSE 
CALL SP_LOAN_AT_RISK();