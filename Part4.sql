/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  dimmkan
 * Created: 31 окт. 2019 г.
 * Создание таблицы подкатегорий
 */
CREATE TABLE `cms`.`subcategories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NOT NULL,
  `parentid` INT NOT NULL,
  PRIMARY KEY (`id`));

/*
* Добавили внешний ключ
*/
ALTER TABLE `cms`.`subcategories` 
ADD INDEX `fk_subcategories_1_idx` (`parentid` ASC);
ALTER TABLE `cms`.`subcategories` 
ADD CONSTRAINT `fk_subcategories_1`
  FOREIGN KEY (`parentid`)
  REFERENCES `cms`.`categories` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

/**
* Добавили колонку в список статей
*/
ALTER TABLE `cms`.`articles` 
ADD COLUMN `subcategoryId` INT(11) NOT NULL AFTER `actions`;

ALTER TABLE `cms`.`articles` 
ADD INDEX `fk_articles_1_idx` (`subcategoryId` ASC);
ALTER TABLE `cms`.`articles` 
ADD CONSTRAINT `fk_articles_1`
  FOREIGN KEY (`subcategoryId`)
  REFERENCES `cms`.`subcategories` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;