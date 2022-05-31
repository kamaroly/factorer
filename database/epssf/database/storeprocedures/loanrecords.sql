/** STORE PROCEDURE TO GENERATE LOAN RECORDS REPORT FOR CEB **/
/** WRITTEN BY KAMARO LAMBERT **/
DROP PROCEDURE IF EXISTS SP_LOAN_RECORDS;
DELIMITER ;;
CREATE PROCEDURE SP_LOAN_RECORDS( IN AdhersionId VARCHAR(25))
BEGIN
    DECLARE n INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE VAR_LOAN_CONTRACT VARCHAR(100) DEFAULT 0;
    DECLARE VAR_ID VARCHAR(100) DEFAULT 0;
    DECLARE VAR_LOAN_START_DATE VARCHAR(100) DEFAULT  DATE('1970-01-01');
    DECLARE VAR_LOAN_END_DATE VARCHAR(100) DEFAULT DATE(current_timestamp);
    DECLARE NEXTLOAN_NUMER  INT DEFAULT 0;
    DECLARE COUNT_SAME_LOANS INT DEFAULT 0;
    DECLARE PREVIOUS_LOAN_CONTRACT VARCHAR(100) DEFAULT 0;
    DECLARE MAXIMUM_LOAN_CONTRACT INT DEFAULT 0;
    
    -- 1. CREATE RESULTS TEMP TABLEL
    DROP TABLE IF EXISTS TEMP_loanRecords;
    CREATE TABLE TEMP_loanRecords(
       id INT(11) AUTO_INCREMENT,
       is_regulation VARCHAR(1),
       created_at  DATETIME,
       loan_contract VARCHAR(200),
       adhersion_id VARCHAR(100),
       movement_nature VARCHAR(200),
       operation_type VARCHAR(200),
       wording VARCHAR(255),
       loan_amount decimal(10,2) ,
       interests decimal(10,2),
       monthly_fees decimal(10,2),
       record_type VARCHAR(200),
       tranches VARCHAR(200),
       loan_id VARCHAR(100),
       transactionid VARCHAR(100),
       cautionneur1 VARCHAR(50),
       cautionneur2 VARCHAR(50),
     primary key (id)
       );
       
    -- 2. GET ALL LOANS FOR THIS MEMBER
    DROP TABLE IF EXISTS TEMP_loans;
    CREATE TEMPORARY TABLE TEMP_loans (
      SELECT id,
       is_regulation,
             CAST(letter_date AS DATETIME) AS created_at,
             CAST(loan_contract as SIGNED) loan_contract,
             adhersion_id,
             movement_nature,
             operation_type,
             comment AS wording,
             loan_to_repay AS loan_amount,
             interests,
             transactionid,
             monthly_fees monthly_fees,
             'loan      ' as record_type,
             0 AS tranches,
             cautionneur1,
             cautionneur2
      FROM loans 
      WHERE adhersion_id= AdhersionId
      ORDER BY CAST(loan_contract as unsigned) ASC,CAST(letter_date AS DATETIME) ASC
      );
      
    -- STORE ALL AVAILABLE CONTRACTS NUMBES
    DROP TABLE IF EXISTS TEMP_contracts;
    CREATE TEMPORARY TABLE TEMP_contracts AS (SELECT distinct loan_contract FROM TEMP_loans);
    TRUNCATE TABLE TEMP_contracts;
    -- LOOP ALL LOANS
  SELECT COUNT(1) INTO n FROM TEMP_loans ;
  SET i=0;
  WHILE (i< n) DO 
  
    SET VAR_LOAN_END_DATE = DATE(current_timestamp);
     -- 3 FETCH LOAN 
  SELECT   l.loan_contract into VAR_LOAN_CONTRACT FROM TEMP_loans  as l ORDER BY loan_contract,created_at LIMIT i,1;
  SELECT   l.id INTO VAR_ID  FROM TEMP_loans  as l ORDER BY loan_contract,created_at LIMIT i,1;
  
      -- INSERT LOAN TO FINAL RESULTS TABLE
      INSERT INTO TEMP_loanRecords SELECT NULL, 
                                    is_regulation, 
                                    created_at,
                                    loan_contract, 
                                    adhersion_id, 
                                    movement_nature, 
                                    operation_type, 
                                    wording, 
                                    loan_amount, 
                                    interests, 
                                    monthly_fees, 
                                    record_type,
                                    tranches,
                                    TEMP_loans.id as loan_id,
                                    transactionid,
                                    cautionneur1,
                                    cautionneur2
                FROM TEMP_loans WHERE loan_contract = VAR_LOAN_CONTRACT AND id = VAR_ID;
      
      -- IF WE HAVE MULTIPLE LOANS WITH SAME LOAN CONTRACT 
      SELECT count(1) into COUNT_SAME_LOANS FROM TEMP_loans WHERE loan_contract=VAR_LOAN_CONTRACT;
      
      IF COUNT_SAME_LOANS > 1
     THEN
         SET MAXIMUM_LOAN_CONTRACT = MAXIMUM_LOAN_CONTRACT + 1;
         SET NEXTLOAN_NUMER = i + 1;
           SELECT  created_at INTO VAR_LOAN_END_DATE  FROM TEMP_loans  ORDER BY loan_contract,created_at LIMIT NEXTLOAN_NUMER,1;
       END IF;
    
    -- SET DEFAULT STARTING DATE
    SELECT  created_at INTO VAR_LOAN_START_DATE  FROM TEMP_loans ORDER BY loan_contract,created_at LIMIT i,1;
    
      -- 
      IF(VAR_LOAN_CONTRACT<> PREVIOUS_LOAN_CONTRACT)
       THEN
      SET PREVIOUS_LOAN_CONTRACT = VAR_LOAN_CONTRACT;
            SET NEXTLOAN_NUMER = 0;
            SET VAR_LOAN_START_DATE = DATE('1970-01-01');
      END IF;
      
      -- IF WE REACHED LATEST LOAN WITH THIS LOAN CONTRACT THEN SET END TIME TO ENDLESS
      IF(COUNT_SAME_LOANS = MAXIMUM_LOAN_CONTRACT)
       THEN
            SET MAXIMUM_LOAN_CONTRACT = 0;
            SET VAR_LOAN_END_DATE = DATE(current_timestamp);
      END IF;
      -- INSERT REFUNDS INTO results table
      INSERT INTO TEMP_loanRecords
                       SELECT   null as id,
                                0 is_regulation,
                                CAST(created_at AS DATETIME) as letter_date,
                                contract_number as loan_contract,
                                adhersion_id,
                                'refund' AS movement_nature,
                                'refund' AS operation_type,
                                wording,
                                0 as loan_amount,
                                0 as interests,
                                0 as monthly_fees,
                                'refund' as record_type,
                                amount as tranches,
                                0 loan_id,
                                0 transactionid,
                                '' as cautionneur1,
                                '' as cautionneur2
                        FROM    refunds WHERE adhersion_id = AdhersionId AND contract_number =VAR_LOAN_CONTRACT
                                AND (DATE(CAST(created_at AS DATETIME)) > DATE(VAR_LOAN_START_DATE) AND DATE(CAST(created_at AS DATETIME)) <=  DATE(VAR_LOAN_END_DATE) )
                                ORDER BY created_at;
    SET i = i + 1;
  END WHILE;
    SELECT * FROM TEMP_loanRecords ORDER BY ID;
End;
;;

CALL SP_LOAN_RECORDS(20071431);

