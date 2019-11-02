<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Subcategory
 *
 * @author dimmkan
 * Класс для работы с подкатегорией
 */
class Subcategory {
    /**
     * @var int идентификатор записи 
     */
    public $id = null;
    /**
     * @var string название подкатегории 
     */
    public $description = null;
    /**
     * @var int идентификатор категории-родителя 
     */
    public $parentid = null;
    /**
     * Конструктор класса
     * @param array $data Массив полей для конструктора
     */
    public function __construct($data = array()) {
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        
        if(isset($data['description'])){
            $this->description = $data['description'];
        }
        
        if(isset($data['parentid'])){
            $this->parentid = $data['parentid'];
        }
    }
    /**
     * Возвращает список подкатегорий
     * @return array
     */
    public static function getList($parentId = ""){
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $par = "";
        if($parentId != "") $par = " WHERE parentid = :parentid";
        $sql = "SELECT * "
                . "FROM subcategories".$par;
        $st = $conn->prepare($sql);
        if($par != "") $st->bindValue (":parentid", $parentId, PDO::PARAM_INT);
        $st->execute();
        
        $list = array();
        
        while ($row = $st->fetch()) {
            $subcategory = new Subcategory($row);
            $list[] = $subcategory;
        }
        
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $conn->query($sql)->fetch();
        $conn = null;
        
        return (array(
            "results" => $list, 
            "totalRows" => $totalRows[0]
            ) 
        );
    }
    /**
     * Поиск подкатегории по идентификатору
     * @param int $id
     * @return \Subcategory
     */
    public static function getById($id) {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * "
                . "FROM subcategories WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
        
        if ($row) { 
            return new Subcategory($row);
        }
    }
    /**
     * Вставить в базу новый объект
     */
    public function insert() {
        if ( !is_null( $this->id ) ) trigger_error ( "Subcategory::insert(): Объект с таким ID уже есть в базе: $this->id).", E_USER_ERROR );

        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "INSERT INTO subcategories (description, parentid) VALUES (:description, :parentid)";
        $st = $conn->prepare ( $sql );
        $st->bindValue( ":description", $this->description, PDO::PARAM_STR);
        $st->bindValue( ":parentid", $this->parentid, PDO::PARAM_INT );
        $st->execute();
        $this->id = $conn->lastInsertId();
        $conn = null;
    }    
    /**
    * Обновляем текущий объект в базе данных
    */
    public function update() {
      if (is_null($this->id)) trigger_error ( "Subcategory::update(): "
              . "Для объекта не задан идентификатор", E_USER_ERROR );

      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $sql = "UPDATE subcategories SET description=:description, parentid=:parentid WHERE id = :id";      
      $st = $conn->prepare ( $sql );
      $st->bindValue(":description", $this->description, PDO::PARAM_STR);
      $st->bindValue(":parentid", $this->parentid, PDO::PARAM_INT);
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
      $conn = null;
    }
    
    /**
    * Удаляем текущий объект из базы данных
    */
    public function delete() {
        if (is_null($this->id))
            trigger_error("Subcategory::delete(): "
                    . "Для объекта не задан идентификатор", E_USER_ERROR);

        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $st = $conn->prepare("DELETE FROM subcategories WHERE id = :id LIMIT 1");
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();
        $conn = null;
    }

}
