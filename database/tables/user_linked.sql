CREATE TABLE `bar_user_linked` (
	`linked_id` INT(11) NOT NULL AUTO_INCREMENT,
	`linked_owner_user_id` INT(11) NOT NULL,
	`linked_user_id` INT(11) NOT NULL,
	`linked_creation_datetime` DATETIME NOT NULL,
	`linked_usage_datetime` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`linked_id`)
)
ENGINE=InnoDB
;
