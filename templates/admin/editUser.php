<?php
/**
 * Создание/изменение пользователя
 */?>
<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>
<h1><?php echo $results['pageTitle']?></h1>
        <form action="admin.php?action=<?php echo $results['formAction']?>" method="post">
            <input type="hidden" name="userId" value="<?php echo $results['user']->id ?>">

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>
            <ul>
              <li>
                <label for="login">Имя пользователя</label>
                <input type="text" name="login" id="login" placeholder="Имя пользователя" required autofocus maxlength="250" value="<?php echo htmlspecialchars( $results['user']->login )?>" />
              </li>

              <li>
                <label for="password">Пароль</label>
                <input type="password" name="password" id="password" placeholder="Пароль" required autofocus maxlength="250" value="<?php echo htmlspecialchars( $results['user']->password )?>" />
              </li>
              <li>
                  <label for="actions">Активность</label>                  
                  <input type="checkbox" name="actions" <?php echo ($results['user']->actions ? " checked" : "") ?> />                 
              </li>


            </ul>

            <div class="buttons">
              <input type="submit" name="saveChanges" value="Сохранить" />
              <input type="submit" formnovalidate name="cancel" value="Отмена" />
            </div>

        </form>

    <?php if ($results['user']->id) { ?>
          <p><a href="admin.php?action=deleteUser&amp;userId=<?php echo $results['user']->id ?>" onclick="return confirm('Удалить пользователя?')">
                  Удалить пользователя
              </a>
          </p>
    <?php } ?>
	  
<?php include "templates/include/footer.php" ?>

