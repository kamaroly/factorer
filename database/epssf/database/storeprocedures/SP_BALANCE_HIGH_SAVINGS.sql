CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_BALANCE_HIGH_SAVINGS`()
BEGIN

DROP TABLE IF EXISTS TEMP_MEMBER_LOAN_REFUND;
CREATE TEMPORARY TABLE TEMP_MEMBER_LOAN_REFUND(
                        SELECT a.adhersion_id,
                              CASE WHEN sum_refund is null THEN sum_loan ELSE sum_loan  - sum_refund end as balance FROM
                        (
                            SELECT adhersion_id,sum(loan_to_repay) as sum_loan FROM 
                                loans as a where  a.status='approved' group by adhersion_id) as a
                            LEFT JOIN 
                        (
                            SELECT adhersion_id,case when sum(amount) is null then 0 else sum(amount) end as sum_refund 
                                FROM refunds  group by adhersion_id) as b 
                            ON a.adhersion_id = b.adhersion_id
                        );


DROP TABLE IF EXISTS TEMP_MEMBER_SAVINGS;
CREATE TEMPORARY TABLE TEMP_MEMBER_SAVINGS(
                SELECT  a.adhersion_id,a.first_name,a.last_name,a.institution,a.status,amount as savings  FROM 
                (SELECT a.adhersion_id,a.first_name,a.last_name,b.name as institution,status FROM users as a,
                institutions b where a.institution_id = b.id and a.adhersion_id<>'200799999' and a.service is not null) as a
                    LEFT JOIN 
                
                (
                SELECT savings.adhersion_id, 
                    case when withdrawal.amount is null then savings.amount
                    else savings.amount - withdrawal.amount end as amount 
                FROM
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='saving' group by adhersion_id) as savings
                  LEFT JOIN
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='withdrawal' group by adhersion_id) as withdrawal
                 ON savings.adhersion_id = withdrawal.adhersion_id
                 )
                 c ON a.adhersion_id = c.adhersion_id);



--  PUT ALL THINGS TOGETHER
     select a.adhersion_id as adhersion_id,a.first_name as first_name,a.last_name as last_name,a.institution as institution,a.status as statuss,a.savings as savings,b.balance as balance,(b.balance-a.savings) as solde from TEMP_MEMBER_SAVINGS as  a left join  TEMP_MEMBER_LOAN_REFUND as b on a.adhersion_id=b.adhersion_id
     where balance>0 and (b.balance-a.savings)>0
     order by a.adhersion_id ;


END