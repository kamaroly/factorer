DROP PROCEDURE IF EXISTS SP_MEMBERS_WITH_LOANS;

DELIMITER //
CREATE PROCEDURE SP_MEMBERS_WITH_LOANS(IN INSTITUTIONID INT(11), IN userStatuses VARCHAR(100))
BEGIN
	# 1. GET ALL MEMBERS LOAN
	DROP TABLE IF EXISTS temp_total_loans;
	CREATE TEMPORARY TABLE temp_total_loans AS 
			SELECT 
				adhersion_id, 
				sum(loan_to_repay) total_loan_amount
			FROM loans 
			WHERE status = 'approved' GROUP BY 1;

	# 2. GET ALL LOAN REFUNDS
	DROP TABLE IF EXISTS temp_total_loan_refunds;
	CREATE TEMPORARY TABLE  temp_total_loan_refunds
	  SELECT adhersion_id, sum(amount) total_loan_refunds FROM refunds GROUP BY 1;
	  
	# 3. GET ALL MEMBERS WITH ACTIVE LOANS
	DROP TABLE IF EXISTS temp_member_with_loans;
	CREATE TEMPORARY TABLE temp_member_with_loans
		SELECT 
			U.first_name,
			U.last_name,
			U.service,
			L.*,
			R.total_loan_refunds,
			(L.total_loan_amount - R.total_loan_refunds) AS balance
		FROM temp_total_loans L LEFT JOIN 
			 temp_total_loan_refunds R ON L.adhersion_id = R.adhersion_id
			 INNER JOIN users U ON L.adhersion_id = u.adhersion_id
		WHERE 
			 L.total_loan_amount > R.total_loan_refunds AND
			 FIND_IN_SET(U.status, userStatuses);
			 
	# 4. GET LATEST LOAN WITHOUT RELICAT OR EMERGENCY, EMERGENCY WILL BE ADDED LATER ON
	DROP TABLE IF EXISTS temp_latest_member_loan;
	CREATE TEMPORARY TABLE temp_latest_member_loan 
		 SELECT 
			adhersion_id, 
			max(created_at) as latestloandate 
		 FROM loans 
		 WHERE status = 'approved' AND operation_type NOT IN('loan_relicat', 'emergency_loan')
		 GROUP BY adhersion_id;


	# 5. ADD NON EMERGENCY MONTHLY FEES 
	DROP TABLE IF EXISTS temp_latest_member_loan_with_fees;
	CREATE TEMPORARY TABLE temp_latest_member_loan_with_fees
		SELECT
			temp_latest_member_loan.adhersion_id,
			loans.monthly_fees 
		FROM temp_latest_member_loan LEFT JOIN loans
		ON temp_latest_member_loan.adhersion_id = loans.adhersion_id AND
		   temp_latest_member_loan.latestloandate = loans.created_at;

	# 6. GET NON PAID EMERGENCY LOANS 
    ## GET PENDING EMERGENCY BASED ON THE REFUNDS
    DROP TABLE IF EXISTS temp_pending_emergency_loans;
	CREATE TEMPORARY TABLE temp_pending_emergency_loans
		SELECT * FROM (
			SELECT
				loans.adhersion_id,
				loans.id as loan_id,
				MAX(loans.loan_to_repay) AS loan_amount,
				sum(refunds.amount)  AS refund_amount
			FROM loans LEFT JOIN refunds 
			ON loans.id = refunds.loan_id 
			WHERE is_umergency = 1 AND status = 'approved'
			GROUP BY loans.adhersion_id, loans.id
         ) AS emergencies WHERE loan_amount > refund_amount;
	
    ## GET FINAL EMERGENCY
	DROP TABLE IF EXISTS temp_non_paid_emergency_loans;
	CREATE TEMPORARY TABLE temp_non_paid_emergency_loans
		 SELECT
			adhersion_id,
			monthly_fees AS emergency_fees,
			emergency_balance,
			emergency_refund,
			monthly_fees AS emergency_monthly_fees,
			tranches_number AS emergency_tranches_number
		 FROM loans 
		 WHERE
			is_umergency = 1 AND status = 'approved' AND
            id IN (SELECT DISTINCT loan_id FROM temp_pending_emergency_loans);
				
	# 7 . PUTTING ALL THINGS TOGETHER
	SELECT 
		members.*,
		CASE 
			WHEN members.balance = feesEmergency.emergency_balance THEN 0
			ELSE fees.monthly_fees 
		END AS monthly_fees,
		feesEmergency.emergency_fees,
		feesEmergency.emergency_balance,
		feesEmergency.emergency_refund,
		feesEmergency.emergency_monthly_fees,
		feesEmergency.emergency_tranches_number
	FROM 
		temp_member_with_loans AS members LEFT JOIN
		temp_latest_member_loan_with_fees AS fees ON members.adhersion_id = fees.adhersion_id
		LEFT JOIN temp_non_paid_emergency_loans AS feesEmergency 
		ON  members.adhersion_id = feesEmergency.adhersion_id;
END //

CALL SP_MEMBERS_WITH_LOANS(11,'actif,inactif');