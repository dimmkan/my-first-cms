<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>
<h1><?php echo $results['pageTitle']?></h1>
        <form action="admin.php?action=<?php echo $results['formAction']?>" method="post">
            <input type="hidden" name="subcategoryId" value="<?php echo $results['subcategory']->id ?>">

    <?php if (isset($results['errorMessage'])) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>
            <ul>
                
              <li>
                <label for="description">Описание</label>
                <input type="text" name="description" id="description" placeholder="Описание" required autofocus maxlength="250" value="<?php echo htmlspecialchars( $results['subcategory']->description )?>" />
              </li>
              
              <li>
                <label for="parentid">Категория</label>
                <select name="parentid">
                  <option value="0"<?php echo !$results['subcategory']->parentid ? " selected" : ""?>>(none)</option>
                <?php foreach ($results['categories'] as $category) { ?>
                  <option value="<?php echo $category->id?>"<?php echo ($category->id == $results['subcategory']->parentid) ? " selected" : ""?>><?php echo htmlspecialchars($category->name)?></option>
                <?php } ?>
                </select>
              </li>


            </ul>

            <div class="buttons">
              <input type="submit" name="saveChanges" value="Сохранить" />
              <input type="submit" formnovalidate name="cancel" value="Отмена" />
            </div>

        </form>

    <?php if ($results['subcategory']->id) { ?>
          <p><a href="admin.php?action=deleteSubcategory&amp;subcategoryId=<?php echo $results['subcategory']->id ?>" onclick="return confirm('Удалить пользователя?')">
                  Удалить подкатегорию
              </a>
          </p>
    <?php } ?>
	  
<?php include "templates/include/footer.php" ?>
