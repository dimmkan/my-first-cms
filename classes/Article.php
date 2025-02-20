<?php


/**
 * Класс для обработки статей
 */
class Article
{
    // Свойства
    /**
    * @var int ID статей из базы данны
    */
    public $id = null;

    /**
    * @var int Дата первой публикации статьи
    */
    public $publicationDate = null;

    /**
    * @var string Полное название статьи
    */
    public $title = null;

     /**
    * @var int ID категории статьи
    */
    public $categoryId = null;

    /**
    * @var string Краткое описание статьи
    */
    public $summary = null;

    /**
    * @var string HTML содержание статьи
    */
    public $content = null;
    /**
    * @var string Поле для задания №1 - 50 первых символов
    * поля content + "..."
    */
    public $shortContent = null;
    /**
    * @var int Поле для задания №2 - признак активности
    * 
    */
    public $actions = 0;
    /**
     *
     * @var int subcategoryId - part 4 
     */
    public $subcategoryId = null;
    
    public $authors = [];
    /**
    * Устанавливаем свойства с помощью значений в заданном массиве
    *
    * @param assoc Значения свойств
    */

    /*
    public function __construct( $data=array() ) {
      if ( isset( $data['id'] ) ) {$this->id = (int) $data['id'];}
      if ( isset( $data['publicationDate'] ) ) {$this->publicationDate = (int) $data['publicationDate'];}
      if ( isset( $data['title'] ) ) {$this->title = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['title'] );}
      if ( isset( $data['categoryId'] ) ) {$this->categoryId = (int) $data['categoryId'];}
      if ( isset( $data['summary'] ) ) {$this->summary = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['summary'] );}
      if ( isset( $data['content'] ) ) {$this->content = $data['content'];}
    }*/
    
    /**
     * Создаст объект статьи
     * 
     * @param array $data массив значений (столбцов) строки таблицы статей
     */
    public function __construct($data=array())
    {
        
      if (isset($data['id'])) {
          $this->id = (int) $data['id'];
      }
      
      if (isset( $data['publicationDate'])) {
          $this->publicationDate = (string) $data['publicationDate'];     
      }

      //die(print_r($this->publicationDate));

      if (isset($data['title'])) {
          $this->title = $data['title'];        
      }
      
      if (isset($data['categoryId'])) {
          $this->categoryId = (int) $data['categoryId'];      
      }
      
      if (isset($data['summary'])) {
          $this->summary = $data['summary'];         
      }
      
      if (isset($data['content'])) {
            $this->content = $data['content'];
            /**
             * Создадим анонимную функцию, проверяющую, больше или равна ли длина поля content
             * 50 символов, если больше или равна - обрезаем 50 и добавлем "...", если меньше -
             * выводим всё.
             * 
             * @param string $string Проверяемая строка
             * @param int $start Начальная позиция среза - по умолчанию 0
             * @param int $length Конечная позиция среза - по умолчанию 50
             * @param string $trimmaker Многоточие, добавляемое в конец
             * @return string Результирующая строка
             */
            $func = function($string, $start = 0, $length = 50, $trimmarker = '...'){
                $len = strlen(trim($string));
                $newstring = ( ($len >= $length) && ($len != 0) ) ? rtrim(mb_substr($string, $start, $length - strlen($trimmarker))) . $trimmarker : $string;
                return $newstring;
            };
            $this->shortContent = $func($data['content']);            
      }
      /**
       * Установим поле actions
       */
      if(isset($data['actions'])){
          $this->actions = $data['actions'];
      }
      
      /**
       * Установим поле subcategoryId
       */
      if(isset($data['subcategoryId'])){
          $this->subcategoryId = $data['subcategoryId'];
      }
      /**
       * Поле автора
       */
      if(isset($data['authors'])) {
          foreach($data['authors'] as $author){
              $this->authors[] = $author;
          }
      }
       
    } 

    /**
    * Устанавливаем свойства с помощью значений формы редактирования записи в заданном массиве
    *
    * @param assoc Значения записи формы
    */
    public function storeFormValues ($params) {

      // Сохраняем все параметры
      $this->__construct($params);

      // Разбираем и сохраняем дату публикации
      if ( isset($params['publicationDate']) ) {
        $publicationDate = explode ( '-', $params['publicationDate'] );

        if ( count($publicationDate) == 3 ) {
          list ( $y, $m, $d ) = $publicationDate;
          $this->publicationDate = mktime ( 0, 0, 0, $m, $d, $y );
        }
      }
    }


    /**
    * Возвращаем объект статьи соответствующий заданному ID статьи
    *
    * @param int ID статьи
    * @return Article|false Объект статьи или false, если запись не найдена или возникли проблемы
    */
    public static function getById($id) {
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) "
                . "AS publicationDate FROM articles WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();

        $row = $st->fetch();
        
        $sql = "SELECT DISTINCT userId FROM authors WHERE articleId=:articleId";
        $st = $conn->prepare($sql);
        $st->bindValue(":articleId", $id, PDO::PARAM_INT);
        $st->execute();
        
        $authors = array();
        
        while($author = $st->fetch()){
            $authors[] = $author['userId'];
        }

        $row['authors'] = $authors;
        
        $conn = null;
        
        if ($row) { 
            return new Article($row);
        }
    }


    /**
    * Возвращает все (или диапазон) объекты Article из базы данных
    *
    * @param int $numRows Количество возвращаемых строк (по умолчанию = 1000000)
    * @param int $categoryId Вернуть статьи только из категории с указанным ID
    * @param string $order Столбец, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
    * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
    */
    public static function getList($numRows=1000000, 
            $categoryId=null, $actionFilter="", $order="publicationDate DESC") 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $categoryClause = $categoryId ? "WHERE categoryId = :categoryId" : "";        
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) 
                AS publicationDate
                FROM articles $categoryClause ".$actionFilter."
                ORDER BY  $order  LIMIT :numRows";
        
        $st = $conn->prepare($sql);
//                        echo "<pre>";
//                        print_r($st);
//                        echo "</pre>";
//                        Здесь $st - текст предполагаемого SQL-запроса, причём переменные не отображаются
        $st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
        
        if ($categoryId) 
            $st->bindValue( ":categoryId", $categoryId, PDO::PARAM_INT);
        
        $st->execute(); // выполняем запрос к базе данных
//                        echo "<pre>";
//                        print_r($st);
//                        echo "</pre>";
//                        Здесь $st - текст предполагаемого SQL-запроса, причём переменные не отображаются
        $list = array();

        while ($row = $st->fetch()) {
            $article = new Article($row);
            $list[] = $article;
        }

        
        // Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $conn->query($sql)->fetch();
                
        foreach ($list as $article){
                        
            $sql2 = "SELECT DISTINCT userId FROM authors WHERE articleId=:articleId";
            $st2 = $conn->prepare($sql2);
            $st2->bindValue(":articleId", $article->id, PDO::PARAM_INT);
            $st2->execute();

            $authors = array();

            while ($author = $st2->fetch()) {
                $authors[] = $author['userId'];
            }

            $article->authors = $authors;

        }
        $conn = null;
        
        return (array(
            "results" => $list, 
            "totalRows" => $totalRows[0]
            ) 
        );
    }
    
    /**
    * Возвращает все (или диапазон) объекты Article из базы данных по subcatId
    *
    * @param int $numRows Количество возвращаемых строк (по умолчанию = 1000000)
    * @param int $subcategoryId Вернуть статьи только из подкатегории с указанным ID
    * @param string $order Столбец, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
    * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
    */
    public static function getListBySubcatId($numRows=1000000, 
            $subcategoryId=null, $actionFilter="", $order="publicationDate DESC") 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $subcategoryClause = $subcategoryId ? "WHERE subcategoryId = :subcategoryId" : "";        
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) 
                AS publicationDate
                FROM articles $subcategoryClause ".$actionFilter."
                ORDER BY  $order  LIMIT :numRows";
        
        $st = $conn->prepare($sql);

        $st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
        
        if ($subcategoryId) 
            $st->bindValue( ":subcategoryId", $subcategoryId, PDO::PARAM_INT);
        
        $st->execute();
        $list = array();

        while ($row = $st->fetch()) {
            $article = new Article($row);
            $list[] = $article;
        }

        // Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $conn->query($sql)->fetch();
        
        foreach ($list as $article){
                        
            $sql2 = "SELECT DISTINCT userId FROM authors WHERE articleId=:articleId";
            $st2 = $conn->prepare($sql2);
            $st2->bindValue(":articleId", $article->id, PDO::PARAM_INT);
            $st2->execute();

            $authors = array();

            while ($author = $st2->fetch()) {
                $authors[] = $author['userId'];
            }

            $article->authors = $authors;

        }
        
        $conn = null;
        
        return (array(
            "results" => $list, 
            "totalRows" => $totalRows[0]
            ) 
        );
    }

    
    public static function getListByAuthor($userId, $numRows = 1000000, $actionFilter = "", $order = "publicationDate DESC") {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);        
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) 
                AS publicationDate
                FROM articles " . $actionFilter . "
                ORDER BY  $order  LIMIT :numRows";

        $st = $conn->prepare($sql);

        $st->bindValue(":numRows", $numRows, PDO::PARAM_INT);

        $st->execute();
        $list = array();

        while ($row = $st->fetch()) {            
            $article = new Article($row);
            $list[] = $article;
        }

        
        $res = array();
        $totalRows = 0;
        $sql2 = "SELECT DISTINCT articleId FROM authors WHERE userId=:userId";
        $st2 = $conn->prepare($sql2);
        $st2->bindValue(":userId", $userId, PDO::PARAM_INT);
        $st2->execute();
        
        while($row = $st2->fetch()){
            foreach ($list as $article){
                if($article->id == $row['articleId']){
                    $res[] = $article;
                    $totalRows++;
                }                
            }
        }
                       
        // Получаем общее количество статей, которые соответствуют критерию
        
        $conn = null;

        return (array(
            "results" => $res,
            "totalRows" => $totalRows
                )
                );
    }

    /**
    * Вставляем текущий объек Article в базу данных, устанавливаем его ID.
    */
    public function insert() {

        // Есть уже у объекта Article ID?
        if ( !is_null( $this->id ) ) trigger_error ( "Article::insert(): Attempt to insert an Article object that already has its ID property set (to $this->id).", E_USER_ERROR );

        // Вставляем статью
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "INSERT INTO articles ( publicationDate, categoryId, title, summary, content, actions, subcategoryId) VALUES ( FROM_UNIXTIME(:publicationDate), :categoryId, :title, :summary, :content, :actions, :subcategoryId)";
        $st = $conn->prepare ( $sql );
        $st->bindValue( ":publicationDate", $this->publicationDate, PDO::PARAM_INT );
        $st->bindValue( ":categoryId", $this->categoryId, PDO::PARAM_INT );
        $st->bindValue( ":subcategoryId", $this->subcategoryId, PDO::PARAM_INT );
        $st->bindValue( ":title", $this->title, PDO::PARAM_STR );
        $st->bindValue( ":summary", $this->summary, PDO::PARAM_STR );
        $st->bindValue( ":content", $this->content, PDO::PARAM_STR );
        $st->bindValue( ":actions", $this->actions, PDO::PARAM_STR );
        $st->execute();
        $this->id = $conn->lastInsertId();
        
        foreach ($this->authors as $userId) {
            $sql = "INSERT INTO authors (userId, articleId) 
                VALUES (:userId, :articleId)";
            $st = $conn->prepare($sql);
            $st->bindValue(":userId", $userId, PDO::PARAM_INT);
            $st->bindValue(":articleId", $this->id, PDO::PARAM_INT);
            $st->execute();
        }
        
        $conn = null;
    }

    /**
    * Обновляем текущий объект статьи в базе данных
    */
    public function update() {
      // Есть ли у объекта статьи ID?
        if (is_null($this->id))
            trigger_error("Article::update(): "
                    . "Attempt to update an Article object "
                    . "that does not have its ID property set.", E_USER_ERROR);

        // Обновляем статью
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "UPDATE articles SET publicationDate=FROM_UNIXTIME(:publicationDate),"
                . " categoryId=:categoryId, title=:title, summary=:summary,"
                . " content=:content, actions=:actions, subcategoryId=:subcategoryId WHERE id = :id";

        $st = $conn->prepare($sql);
        $st->bindValue(":publicationDate", $this->publicationDate, PDO::PARAM_INT);
        $st->bindValue(":categoryId", $this->categoryId, PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $this->subcategoryId, PDO::PARAM_INT);
        $st->bindValue(":title", $this->title, PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, PDO::PARAM_STR);
        $st->bindValue(":actions", $this->actions, PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();


        $sql = "DELETE FROM authors WHERE articleId=:articleId";
        $st = $conn->prepare($sql);
        $st->bindValue(":articleId", $this->id, PDO::PARAM_INT);
        $st->execute();

        foreach ($this->authors as $userId) {
            $sql = "INSERT INTO authors (userId, articleId) 
                VALUES (:userId, :articleId)";
            $st = $conn->prepare($sql);
            $st->bindValue(":userId", $userId, PDO::PARAM_INT);
            $st->bindValue(":articleId", $this->id, PDO::PARAM_INT);
            $st->execute();
        }
        $conn = null;
    }


    /**
    * Удаляем текущий объект статьи из базы данных
    */
    public function delete() {

      // Есть ли у объекта статьи ID?
      if ( is_null( $this->id ) ) trigger_error ( "Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR );

      
      // Удаляем статью
      $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
      
      $sql = "DELETE FROM authors WHERE articleId=:articleId";
      $st = $conn->prepare($sql);
      $st->bindValue(":articleId", $this->id, PDO::PARAM_INT);
      $st->execute();

      $st = $conn->prepare("DELETE FROM articles WHERE id = :id LIMIT 1");
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
      $conn = null;
    }

}
