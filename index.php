<?php

//phpinfo(); die();

require("config.php");

try {
    initApplication();
} catch (Exception $e) { 
    $results['errorMessage'] = $e->getMessage();
    require(TEMPLATE_PATH . "/viewErrorPage.php");
}


function initApplication()
{
    $action = isset($_GET['action']) ? $_GET['action'] : "";

    switch ($action) {
        case 'archive':
          archive();
          break;
        case 'viewArticle':
          viewArticle();
          break;
        case 'viewArticleByAuthor':
          viewArticleByAuthor();
          break;
        default:
          homepage();
    }
}

function archive() {
    $results = [];
    
    if (isset($_GET['subcategoryId'])) {
        $subcategoryId = ($_GET['subcategoryId']);
        $results['subcategory'] = Subcategory::getById($subcategoryId);

        $data = Article::getListBySubcatId(100000, $results['subcategory'] ? $results['subcategory']->id : null,
                        $results['subcategory'] ? " AND actions = 1" : "WHERE actions = 1");

        $results['articles'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];

        $data = Subcategory::getList();
        $results['subcategories'] = array();

        foreach ($data['results'] as $subcategory) {
            $results['subcategories'][$subcategory->id] = $subcategory;
        }

        $data = User::getList();
        $results['authors'] = $data['results'];
        
        $results['pageHeading'] = $results['subcategory'] ? $results['subcategory']->description : "Article Archive";
        $results['pageTitle'] = $results['pageHeading'] . " | Widget News";

        require( TEMPLATE_PATH . "/archive.php" );
    }else {
        $categoryId = (isset($_GET['categoryId']) && $_GET['categoryId']) ? (int) $_GET['categoryId'] : null;

        $results['category'] = Category::getById($categoryId);

        $data = Article::getList(100000, $results['category'] ? $results['category']->id : null,
                        $results['category'] ? " AND actions = 1" : "WHERE actions = 1");

        $results['articles'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];

        $data = Category::getList();
        $results['categories'] = array();

        foreach ($data['results'] as $category) {
            $results['categories'][$category->id] = $category;
        }
        
        $data = User::getList();
        $results['authors'] = $data['results'];
        
        $results['pageHeading'] = $results['category'] ? $results['category']->name : "Article Archive";
        $results['pageTitle'] = $results['pageHeading'] . " | Widget News";

        require( TEMPLATE_PATH . "/archive.php" );
    }
}

/**
 * Отбор по автору
 */
function viewArticleByAuthor(){
    $results = [];
    if(isset($_GET['authorId'])){
        $data = Article::getListByAuthor($_GET['authorId']);
        $results['articles'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];

        $results['author'] = User::getUserByID($_GET['authorId'])->login;
        
        $results['pageHeading'] = $results['author'];
        $results['pageTitle'] = $results['pageHeading'] . " | Widget News";

        require( TEMPLATE_PATH . "/viewArticleByAuthor.php" );
    }else {
        require(TEMPLATE_PATH . "/homepage.php");
    }
}
/**
 * Загрузка страницы с конкретной статьёй
 * 
 * @return null
 */
function viewArticle() 
{   
    if ( !isset($_GET["articleId"]) || !$_GET["articleId"] ) {
      homepage();
      return;
    }

    $results = array();
    $articleId = (int)$_GET["articleId"];
    $results['article'] = Article::getById($articleId);
    
    if (!$results['article']) {
        throw new Exception("Статья с id = $articleId не найдена");
    }
    
    $results['category'] = Category::getById($results['article']->categoryId);
    $results['pageTitle'] = $results['article']->title . " | Простая CMS";
    
    $data = User::getList();
    $results['authors'] = $data['results'];
    
    require(TEMPLATE_PATH . "/viewArticle.php");
}

/**
 * Вывод домашней ("главной") страницы сайта
 */
function homepage() 
{
    $results = array();
    $data = Article::getList(HOMEPAGE_NUM_ARTICLES, null, "WHERE actions = 1");
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    $data = Category::getList();
    $results['categories'] = array();
    foreach ( $data['results'] as $category ) { 
        $results['categories'][$category->id] = $category;
    }
    
    $data = Subcategory::getList();
    $results['subcategories'] = array();
    
    foreach ( $data['results'] as $subcategory ) {
        $results['subcategories'][$subcategory->id] = $subcategory;
    }
    
    $data = User::getList();
    $results['authors'] = $data['results'];
    
    $results['pageTitle'] = "Простая CMS на PHP";
    
//    echo "<pre>";
//    print_r($data);
//    echo "</pre>";
//    die();
    
    require(TEMPLATE_PATH . "/homepage.php");
    
}