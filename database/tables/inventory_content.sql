CREATE TABLE `bar_inventory_content` (
	`content_id` INT(11) NOT NULL AUTO_INCREMENT,
	`content_inventory_id` INT(11) NOT NULL,
	`content_product_id` INT(11) NOT NULL,
	`content_quantity` INT(11) NOT NULL DEFAULT '0',
	`content_creation_datetime` DATETIME NOT NULL,
	`content_modification_datetime` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`content_id`)
)
ENGINE=InnoDB
;
