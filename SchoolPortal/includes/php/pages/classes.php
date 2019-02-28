<?php
  $action = ''; $type = '';
  if (isset($_GET['classes'])) {
    $action = $_GET['classes'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  ?>
    <div class="sub_menu" data-class=<?php echo $type; ?>>
      <?php
        if ($action == 'view') {
          ?>
          <a class="button_link left_align" href="index.php?class_sections=show&type=<?php echo $type; ?>">Sections</a>
          <?php
        }else {
          ?>
          <a class="button_link left_align" href="index.php?classes=add">Add Class</a>
          <?php
        }
       ?>
    </div>
  <?php

    if ($action == 'add') {
      ?>
      <form class="form" id="add_class" action="" method="post">
        <div>
          <label for="title">Title</label>
          <input type="text" name="title" id="title"></input>
        </div>
        <div class="submit">
          <button id="submit">Add Class</button>
        </div>
      </form>
      <?php
    }
    elseif ($action == 'show') {
      ?>
      <div>
        <table class="table">
          <h3 class="title">All Classes</h3>
          <tr>
            <th>S/N</th>
            <th>Title</th>
            <th>Sections</th>
            <th>No of Students</th>
            <th>Delete</th>
          </tr>
          <?php
            if ($type == 'all') {
              $classes = $query->GetRowsFromTable($site.'.classes', '', '', '', '', 'id');
              if (count($classes)) {
                foreach ($classes as $key => $value) {
                  $contents = $value['content'];
                  ?>
                    <tr class="view_classes" id="<?php echo $contents['id'] ?>">
                      <td><?php echo 1+$key?></td>
                      <td><?php echo $contents['title'] ?></td>
                      <td id="class_sections">
                        <a href="index.php?class_sections=show&type=<?php echo $contents['id'] ?>">
                          <?php
                            $sections = $query->GetColumnFromTable($site.'.class_sections', 'class', $contents['id'], '', '', 'section');
                            if (count($sections) == 0) {
                              echo "None";
                            }else {
                              foreach ($sections as $key => $value) {
                                echo $value;
                                if ($key + 1 != count($sections)) {
                                  echo ",";
                                }
                              }
                            }
                          ?>
                        </a>
                      </td>
                      <td>
                        <?php
                          $student_no = $query->GetColumnFromTable($site.'.student', 'class', $contents['id'], '', '', 'e_key');
                          echo count($student_no);
                        ?>
                      </td>
                      <td>
                        <a href="index.php?classes=delete&type=<?php echo $contents['id']; ?>">Delete</a>
                      </td>
                    </tr>
                  <?php
                }
              }
            }
           ?>
        </table>
      </div>
      <?php
    }
    elseif ($action == 'view') {
      $class = $query->GetFromTable($site.".classes", '', '', '', '', '');
      $students = $query->GetRowsFromTable($site.".student", 'class', $type, '', '', 'e_key');
      ?>
      <div class="expandable">
        <div class="expandable_title">Bio-data<span class="toggle toggle_close"></span></div>
        <div class="expandable_content">
          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Title</h4>
              <div class="cell_content editable">
                <p><?php echo $class['title'] ?></p>
                <input class="edit hide" type="text" name="" value="<?php echo $class['title'] ?>" data-table=".<?php echo "classes"; ?>" data-column="title"/>
              </div>
            </div>
            <div class="cell right">
              <h4 class="cell_title">Number of Students</h4>
              <div class="cell_content editable">
                34
              </div>
            </div>
          </div>

          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Sections</h4>
              <div class="cell_content editable">
                A, B, C
              </div>
            </div>
            <a href="index.php?classses=delete&type=<?php echo $type ?>" class="button_link right_align">Delete</a>
          </div>
        </div>
      </div>
      <br><br><br>
      <table class="table">
        <h3 class="title">Students in <?php echo $query->GetFromTable($site.".classes", 'id', $type, '', '', 'title'); ?></h3>
        <tr>
          <th>S/N</th>
          <th>Name</th>
          <th>Section</th>
        </tr>
        <?php
          foreach ($students as $key => $value) {
            $contents = $value['content'];
            ?>
            <tr class="view_student" id="<?php echo $contents['e_key']; ?>">
              <td><?php echo $key + 1 ?></td>
              <td><?php echo $contents['user_name']; ?></td>
              <td><a href="index.php?class_sections=view&type=<?php echo $contents['class_sections'] ?>&class=<?php echo $type ?>"><?php echo $query->GetFromTable($site.".class_sections", "id", $contents['class_sections'], '', '', 'section'); ?></a></td>
            </tr>
            <?php
          }
         ?>
      </table>
      <?php
    }
    elseif ($action == 'delete') {
      if ($query->DeleteFromDB($site.'.classes', 'id', $type)) {
        if ($query->DoesRowsExist($site.'.class_sections', ['class'], [$type])) {
          if ($query->DeleteFromDB($site.'.class_sections', 'class', $type)) {
            $utk->Alert("Class has been deleted");
            $utk->Redirect($_SESSION['referer']);
          }
        }else {
          $utk->Alert("Class has been deleted");
          $utk->Redirect($_SESSION['referer']);
        }
      }
    }
 ?>
