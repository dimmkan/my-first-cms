<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Класс для работы с сущностью "Пользователь"
 *
 * @author dimmkan
 */
class User {
    // Описание полей класса
    /**
     *
     * @var int Идентификатор пользователя 
     */
    public $id = null;
    /**
     *
     * @var string Имя пользователя, поле уникальное в SQL 
     */
    public $login = null;
    /**
     *
     * @var string Пароль пользователя 
     */
    public $password = null;
    /**
     *
     * @var int Признак активности пользователя 
     */
    public $actions = 0;
    /**
     * Конструктор класса
     * 
     * @param array $data Массив полей для конструктора
     */
    public function __construct($data = array()) {
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        
        if(isset($data['login'])){
            $this->login = $data['login'];
        }
        
        if(isset($data['password'])){
            $this->password = $data['password'];
        }
        
        if(isset($data['actions'])){
            $this->actions = $data['actions'];
        }
    }
    /**
     * Функция проверяет наличие пользователя в БД
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public static function authorizedUser($login = "", $password = ""){
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * "
                . "FROM users WHERE login = :login AND password = :password";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $login, PDO::PARAM_STR);
        $st->bindValue(":password", $password, PDO::PARAM_STR);
        $st->execute();
        $row = $st->fetch();
        $conn = null;
        
        if($row){
            return true;
        }else {
            return false;
        }    
    }
    
    public static function actionUser($login = "", $password = ""){
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT actions "
                . "FROM users WHERE login = :login AND password = :password";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $login, PDO::PARAM_STR);
        $st->bindValue(":password", $password, PDO::PARAM_STR);
        $st->execute();
        $row = $st->fetch();
        $conn = null;

        if ($row['actions']) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getList(){
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * "
                . "FROM users";
        $st = $conn->prepare($sql);
        $st->execute();
        
        $list = array();
        
        while ($row = $st->fetch()) {
            $user = new User($row);
            $list[] = $user;
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
     * Получаем пользователя по идентификатору в БД
     * @param integer $id
     * @return \User
     */
    public static function getUserByID($id){
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "SELECT * "
                . "FROM users WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
        
        if ($row) { 
            return new User($row);
        }
    }
    //Сохраняем данные формы
    /**
     * 
     * @param assoc $params
     */
    public function storeFormValues ($params) {  
        
        $this->__construct($params);
    }
    
    /**
    * Вставляем текущий объек User в базу данных, устанавливаем его ID.
    */
    public function insert() {
        // Объект с таким идентификатором уже есть
        if ( !is_null( $this->id ) ) trigger_error ( "User::insert(): Попытка вставить объект с уже существующим ID (to $this->id).", E_USER_ERROR );

        // Записываем пользователя в базу
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "INSERT INTO users (login, password, actions) VALUES (:login, :password, :actions)";
        $st = $conn->prepare ($sql);        
        $st->bindValue(":login", $this->login, PDO::PARAM_STR);
        $st->bindValue(":password", $this->password, PDO::PARAM_STR);
        $st->bindValue(":actions", $this->actions, PDO::PARAM_INT);
        $st->execute();
        $this->id = $conn->lastInsertId();
        $conn = null;
    }
    
    /**
     * Обновление записи в БД
     */
    public function update(){
      // Проверим, есть ли у объекта ID
      if (is_null( $this->id ) ) trigger_error ( "User::update(): "
              . "Невозможно обновить запись объекта - "
              . " не задано значение ID.", E_USER_ERROR );
      // Обновляем запись
      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $sql = "UPDATE users SET"
              . " login=:login, password=:password, actions=:actions"
              . " WHERE id = :id";      
      $st = $conn->prepare ( $sql );
      $st->bindValue( ":login", $this->login, PDO::PARAM_STR );
      $st->bindValue( ":password", $this->password, PDO::PARAM_STR );
      $st->bindValue( ":actions", $this->actions, PDO::PARAM_INT );
      $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
      $st->execute();
      $conn = null;
    }
    
   /**
    * Удаляем текущего пользователя из БД
    */
    public function delete() {
      // Проверим, есть ли у объекта ID
      if (is_null( $this->id ) ) trigger_error ( "User::delete(): Невозможно удалить объект - "
              ." не задан ID объекта.", E_USER_ERROR );

      // Удаляем пользователя
      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $st = $conn->prepare ("DELETE FROM users WHERE id = :id LIMIT 1" );
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
      $conn = null;
    }
    
}
