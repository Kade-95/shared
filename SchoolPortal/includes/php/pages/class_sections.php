<?php
  $action = ''; $type = '';
  if (isset($_GET['class_sections'])) {
    $action = $_GET['class_sections'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  ?>
    <div class="sub_menu" data-class_sections=<?php echo $type; ?>>
      <?php
        if ($action == 'view') {
          ?>
          <?php
        }else {
          ?>
          <a class="button_link left_align" href="index.php?class_sections=add&type=<?php echo $type; ?>">Add Class Section</a>
          <?php
        }
       ?>
    </div>
  <?php
    if ($action == 'add') {
      ?>
      <form class="form" id="add_class_section" action="" method="post">
        <div>
          <label for="section">Section</label>
          <input type="text" name="section" id="section"></input>
        </div>
        <div>
          <label for="form_teacher">Form Teacher</label>
          <select id="form_teacher" name="form_teacher">
            <option selected disabled>Select Form Teacher</option>
            <?php
              $staff = $query->GetColumnFromTable($site.'.staff', '', '', '', '', 'e_key');
              foreach ($staff as $key => $value) {
                $name = $query->GetFromTable($site.'.staff', 'e_key', $value, '', '', 'user_name');
                ?>
                <option value=<?php echo $value; ?>><?php echo $name; ?></option>
                <?php
              }
             ?>
          </select>
        </div>
        <div class="submit">
          <button id="submit">Add Class Section</button>
        </div>
      </form>
      <?php
    }
    elseif ($action == 'show') {
      ?>
      <div>
        <table class="table" data-class=<?php echo $type; ?>>
          <h3 class="title">All <?php echo $query->GetFromTable($site.'.classes', 'id', $type, '', '', 'title'); ?> Class Sections</h3>
          <tr>
            <th>S/N</th>
            <th>Section</th>
            <th>Form Teacher</th>
            <th>No of Students</th>
            <th>Delete</th>
          </tr>
          <?php
            $class_sections = '';
            if ($type == 'all') {
              $class_sections = $query->GetRowsFromTable($site.'.class_sections', '', '', '', '', 'id');
            }else {
              $class_sections = $query->GetRowsFromTable($site.'.class_sections', 'class', $type, '', '', 'id');
            }
            foreach ($class_sections as $key => $value) {
              $contents = $value['content'];
              ?>
                <tr class="view_class_sections" id="<?php echo $contents['id'] ?>">
                  <td><?php echo 1+$key?></td>
                  <td><?php echo $contents['section'] ?></td>
                  <td>
                    <a href="index.php?profile=staff&key=<?php echo $contents['form_teacher'] ?>&owner=staff">
                      <?php
                      echo $query->GetFromTable($site.'.staff', 'e_key', $contents['form_teacher'], '', '', 'user_name');
                      ?>
                    </a>
                  </td>
                  <td>
                    <?php
                      $student_no = $query->GetColumnFromTable($site.'.student', 'class', $type, 'class_sections', $contents['id'], 'e_key');
                      echo count($student_no);
                    ?>
                  </td>
                  <td>
                    <a href="index.php?class_sections=delete&type=<?php echo $contents['id']; ?>">Delete</a>
                  </td>
                </tr>
              <?php
            }
           ?>
        </table>
      </div>
      <?php
    }
    elseif ($action == 'view') {
      $class_sections = $query->GetFromTable($site.".class_sections", 'id', $type, '', '', '');
      $students = $query->GetRowsFromTable($site.".student", 'class_sections', $type, '', '', 'e_key');
      ?>
      <div class="expandable">
        <div class="expandable_title">Bio-data<span class="toggle toggle_close"></span></div>
        <div class="expandable_content">
          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Section</h4>
              <div class="cell_content editable">
                <p><?php echo $class_sections['section'] ?></p>
                <input class="edit hide" type="text" name="" value="<?php echo $class_sections['section'] ?>" data-table=".<?php echo "class_sections"; ?>" data-column="section"/>
              </div>
            </div>
            <div class="cell right">
              <h4 class="cell_title">Form Teacher</h4>
              <div class="cell_content editable">
                <p><?php echo $query->GetFromTable($site.".staff", 'e_key', $class_sections['form_teacher'], '', '', 'user_name'); ?></p>
                <select class="edit hide" name="" data-table=".<?php echo "class_sections"; ?>" data-column="form_teacher">
                  <option selected disabled value="0"><?php echo $query->GetFromTable($site.".staff", 'e_key', $class_sections['form_teacher'], '', '', 'user_name'); ?></option>
                  <?php
                  $staff = $query->GetColumnFromTable($site.'.staff', '', '', '', '', 'e_key');
                  foreach ($staff as $key => $value) {
                    $name = $query->GetFromTable($site.'.staff', 'e_key', $value, '', '', 'user_name');
                    ?>
                    <option value=<?php echo $value; ?>><?php echo $name; ?></option>
                    <?php
                  }                   ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Class</h4>
              <div class="cell_content editable">
                <p><?php echo $query->GetFromTable($site.".classes", 'id', $class_sections['class'], '', '', 'title') ?></p>
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
            <a href="index.php?classses=delete&type=<?php echo $type ?>&class=<?php echo $class_sections['class'] ?>" class="button_link right_align">Delete</a>
          </div>
        </div>
      </div>
      <br><br><br>
      <table class="table">
        <h3 class="title">Students in <?php echo $query->GetFromTable($site.".classes", 'id', $query->GetFromTable($site.'.class_sections', 'id', $type, '', '', 'class'), '', '', 'title'); ?> <?php echo $query->GetFromTable($site.".class_sections", 'id', $type, '', '', 'section'); ?></h3>
        <tr>
          <th>S/N</th>
          <th>Name</th>
        </tr>
        <?php
          foreach ($students as $key => $value) {
            $contents = $value['content'];
            ?>
            <tr class="view_student" id="<?php echo $contents['e_key']; ?>">
              <td><?php echo $key + 1 ?></td>
              <td><?php echo $contents['user_name']; ?></td>
            </tr>
            <?php
          }
         ?>
      </table>
      <?php
    }
    elseif ($action == 'delete') {
      if ($query->DeleteFromDB($site.'.class_sections', 'id', $type)) {
        $utk->Alert("Staff has been deleted");
        $utk->Redirect($_SESSION['referer']);
      }
    }
 ?>
