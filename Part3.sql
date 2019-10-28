/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  dimmkan
 * Created: 26 окт. 2019 г.
 * Создание таблицы пользователей
 */
CREATE TABLE `cms`.`users` (
  `id` INT NOT NULL,
  `login` VARCHAR(255) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `actions` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `login_UNIQUE` (`login` ASC));
/*
* Забыл добавить автоинкремент
*/
ALTER TABLE `cms`.`users` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT ;