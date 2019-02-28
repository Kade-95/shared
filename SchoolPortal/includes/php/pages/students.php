<?php
  $action = ''; $type = '';
  ?>
    <div class="sub_menu">
      <?php
        if ($_SESSION['session'] == 'Global_Admin') {
          ?>
          <a class="button_link left_align" href="index.php?admin=show&type=students">Manage Admins</a>
          <?php
        }
       ?>
    </div>
  <?php

    if (isset($_GET['students'])) {
      $action = $_GET['students'];
    }

    if (isset($_GET['type'])) {
      $type = $_GET['type'];
    }

    if ($action == 'show') {
      ?>
      <div>
        <table class="table">
          <h3 class="title">All Students</h3>
          <tr>
            <th>S/N</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Image</th>
            <th>Class</th>
            <th>Section</th>
            <th>Delete</th>
          </tr>
          <?php
            $students = $query->GetRowsFromTable($site.'.student', '', '', '', '', 'e_key');
            foreach ($students as $key => $value) {
              $contents = $value['content'];
              ?>
                <tr class="view_student" id="<?php echo $contents['e_key'] ?>">
                  <td><?php echo 1+$key?></td>
                  <td><?php echo $contents['full_name'] ?></td>
                  <td><?php echo $contents['email'] ?></td>
                  <td><?php echo $contents['image'] ?></td>
                  <td><a href="index.php?classes=view&type=<?php echo $contents['class']; ?>"><?php echo $query->GetFromTable($site.'.classes', 'id', $contents['class'], '', '', 'title') ?></a></td>
                  <td><a href="index.php?class_sections=view&type=<?php echo $contents['class_sections'] ?>"><?php echo $query->GetFromTable($site.'.class_sections', 'id', $contents['class_sections'], '', '', 'section') ?></a></td>
                  <td>
                    <a href="index.php?students=delete&type=<?php echo $contents['e_key']; ?>">Delete</a>
                  </td>
                </tr>
              <?php
            }
           ?>
        </table>
      </div>
      <?php
    }
    elseif ($action == 'delete') {
      if ($query->DeleteFromDB($site.'.student', 'e_key', $type)) {
        $utk->Alert("Student has been deleted");
        $utk->Redirect($_SESSION['referer']);
      }
    }
 ?>
