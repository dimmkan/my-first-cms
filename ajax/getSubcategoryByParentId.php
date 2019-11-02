<?php
require ('../config.php');

if (isset ($_POST['parentid'])) {
    $article = Subcategory::getList($_POST['parentid']);
    echo json_encode($article);
}
?>