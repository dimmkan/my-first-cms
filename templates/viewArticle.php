<?php include "templates/include/header.php" ?>
	  
    <h1 style="width: 75%;"><?php echo htmlspecialchars( $results['article']->title )?></h1>
    <div style="width: 75%; font-style: italic;"><?php echo htmlspecialchars( $results['article']->summary )?></div>
    <div style="width: 75%;"><?php echo $results['article']->content?></div>
    <p class="pubDate">Published on <?php  echo date('j F Y', $results['article']->publicationDate)?>
    
    <?php if ( $results['category'] ) { ?>
        in 
        <a href="./?action=archive&amp;categoryId=<?php echo $results['category']->id?>">
            <?php echo htmlspecialchars($results['category']->name) ?>
        </a>
    <?php } ?>
        
    </p>
    <span class="category">
        <?php
        $res = "";
        foreach ($results['authors'] as $author) {
            if (in_array($author->id, $results['article']->authors)) {
            ?>
                <a href=".?action=viewArticleByAuthor&amp;authorId=<?php echo (in_array($author->id, $results['article']->authors)) ? $author->id : "0"; ?>"><?php echo(in_array($author->id, $results['article']->authors)) ? htmlspecialchars($author->login) : ""; ?></a>
            <?php
            }
        }
        ?>
    </span>
    <p><a href="./">Вернуться на главную страницу</a></p>
	  
<?php include "templates/include/footer.php" ?>    
                