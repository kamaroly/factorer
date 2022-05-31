/** GETTING ALL MEMBERS LOANS **/
DROP TABLE IF EXISTS TEMP_memberloans;
CREATE TEMPORARY TABLE TEMP_memberloans
(
SELECT adhersion_id,sum(loan_to_repay) loansAmount FROM loans where `status` = 'approved' group by adhersion_id
);

/** GETTING ALL MEMBERS REFUNDS **/
DROP TABLE IF EXISTS TEMP_memberrefunds;
CREATE TEMPORARY TABLE TEMP_memberrefunds
(
SELECT adhersion_id,sum(amount) refundedAmount FROM refunds group by adhersion_id
);
/** GETTING MEMBER WITH LOANS **/
DROP TABLE IF EXISTS TEMP_members_with_active_loans;
CREATE TEMPORARY TABLE TEMP_members_with_active_loans
(
 SELECT a.adhersion_id,loansAmount,refundedAmount,first_name,last_name,service FROM TEMP_memberloans AS a,
 TEMP_memberrefunds as b,
 users c WHERE  a.loansAmount > b.refundedAmount AND a.adhersion_id = b.adhersion_id
 AND  a.adhersion_id = c.adhersion_id
);


DROP TABLE IF EXISTS TEMP_member_latest_refund;
CREATE TEMPORARY TABLE TEMP_member_latest_refund
(
SELECT * FROM
			(
			SELECT 
					adhersion_id,
			        max(created_at) as last_date
					FROM refunds 
					GROUP BY 
					adhersion_id
			) AS a
			WHERE 
			/* Smaller or equal than one month ago */
			last_date < DATE_SUB(NOW(), INTERVAL 3 MONTH)
);

DROP TABLE IF EXISTS TEMP_member_latest_loan;
CREATE TEMPORARY TABLE TEMP_member_latest_loan
(
SELECT a.adhersion_id, a.monthly_fees,a.comment FROM loans as a,
(SELECT * FROM
			(
			SELECT 
					adhersion_id,
			        max(id) as id
					FROM loans 
                    WHERE `status` ='approved'
					GROUP BY 
					adhersion_id
			) AS a
 ) as b
 WHERE a.id = b.id AND a.adhersion_id = b.adhersion_id
);

DROP TABLE IF EXISTS TEMP_contributions;
CREATE TEMPORARY TABLE TEMP_contributions
(
SELECT 
					adhersion_id,
			        sum(amount) as contributed_amount,
			        max(created_at) as last_date
					FROM contributions 
			        WHERE transaction_type = 'saving'
						GROUP BY 
					adhersion_id
);

SELECT a.adhersion_id,
       a.first_name,
       a.last_name,
       loansAmount,
       refundedAmount,
       b.last_date,
       c.monthly_fees,
       c.comment,
       d.contributed_amount 
FROM TEMP_members_with_active_loans as a,
TEMP_member_latest_refund as b,
 TEMP_member_latest_loan c,
 TEMP_contributions as d
WHERE   a.adhersion_id = b.adhersion_id 
AND   b.adhersion_id = c.adhersion_id 
AND  b.adhersion_id = d.adhersion_id

