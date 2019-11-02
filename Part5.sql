/**
* Создание промежуточной таблицы
*/
CREATE TABLE `cms`.`authors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `articleId` SMALLINT(5) UNSIGNED NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_authors_1_idx` (`articleId` ASC),
  INDEX `fk_authors_2_idx` (`userId` ASC),
  CONSTRAINT `fk_authors_1`
    FOREIGN KEY (`articleId`)
    REFERENCES `cms`.`articles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_authors_2`
    FOREIGN KEY (`userId`)
    REFERENCES `cms`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);