

CREATE TABLE `calculo`.`date_interval` (
    `id` INT(255) NOT NULL AUTO_INCREMENT,
    `amount` INT(255) NOT NULL,
    `datetime` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `calculo`.`account` (
    `username` VARCHAR (255) NOT NULL,
    `password` VARCHAR (255) NOT NULL,
    `type` ENUM ('admin', 'employee') NOT NULL,
    PRIMARY KEY (`username`)
);
