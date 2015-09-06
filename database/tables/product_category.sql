CREATE TABLE `bar_product_category` (
	`product_category_id` INT(11) NOT NULL AUTO_INCREMENT,
	`product_category_parent_id` INT(11) NULL DEFAULT NULL,
	`product_category_name` TEXT NOT NULL,
	`product_category_name_translations` TEXT NULL,
	`product_category_creation_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`product_category_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
