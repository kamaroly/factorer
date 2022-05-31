-- SET RIGHT DEFAULT VALUES
ALTER TABLE `ceb`.`loans` 
CHANGE COLUMN `letter_date` `letter_date` TIMESTAMP NULL ,
CHANGE COLUMN `created_at` `created_at` TIMESTAMP NULL ,
CHANGE COLUMN `updated_at` `updated_at` TIMESTAMP NULL ;

-- ADDING INDEX TO LOANS TABLE
ALTER TABLE `ceb`.`loans` ADD INDEX `created_at` (`created_at` ASC);


