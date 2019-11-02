<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

<h1>Все подкатегории</h1>

    <?php if (isset($results['errorMessage'])) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>


    <?php if (isset($results['statusMessage'])) { ?>
            <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
    <?php } ?>

          <table>
            <tr>
              <th>Описание</th>
              <th>Категория</th>
            </tr>
            
    <?php foreach ($results['subcategories'] as $subcategory) { ?>

                <tr onclick="location='admin.php?action=editSubcategory&amp;subcategoryId=<?php echo $subcategory->id ?>'">
                    <td><?php echo $subcategory->description ?></td>
                    <td>
                        <?php
                        if (isset($subcategory->parentid)) {
                            echo $results['categories'][$subcategory->parentid]->name;
                        } else {
                            echo "Без категории";
                        }
                        ?>
                    </td>
                </tr>

    <?php } ?>

          </table>

          <p><?php echo $results['totalRows']?> подкатегорий всего.</p>

          <p><a href="admin.php?action=newSubcategory">Создать подкатегорию</a></p>

<?php include "templates/include/footer.php" ?> 