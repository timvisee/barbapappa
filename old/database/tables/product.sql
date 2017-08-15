CREATE TABLE `bar_product` (
	`product_id` INT(11) NOT NULL AUTO_INCREMENT,
	`product_category_id` INT(11) NULL DEFAULT NULL,
	`product_name` TEXT NOT NULL,
	`product_name_translations` TEXT NULL,
	`product_price` INT(11) NOT NULL DEFAULT '0',
	`product_creation_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`product_id`)
)
ENGINE=InnoDB
;
