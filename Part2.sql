/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  dimmkan
 * Created: 24 окт. 2019 г.
 * Создание колонки "actions" в таблице Articles
 */
ALTER TABLE cms.articles ADD COLUMN actions tinyint NOT NULL AFTER content;
ALTER TABLE cms.articles ALTER actions SET DEFAULT 0;

