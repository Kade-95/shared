<?php
  $action = ''; $type = '';
  if (isset($_GET['subjects'])) {
    $action = $_GET['subjects'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  ?>
    <div class="sub_menu" data-subject=<?php echo $type; ?>>
      <?php
        if ($action == 'view') {
          ?>
          <a href="index.php?subjects=allocate&type=<?php echo $type ?>" class="button_link">Allocate subject</a>
          <?php
        }else {
          ?>
          <a class="button_link left_align" href="index.php?subjects=add">Add Subject</a>
          <?php
        }
       ?>
    </div>
  <?php

    if ($action == 'add') {
      ?>
      <form class="form" id="add_subject" action="" method="post">
        <div>
          <label for="title">Title</label>
          <input type="text" name="title" id="title"></input>
        </div>
        <div class="submit">
          <button id="submit">Add Subject</button>
        </div>
      </form>
      <?php
    }
    elseif ($action == 'show') {
      ?>
      <div>
        <table class="table">
          <h3 class="title">All Subjects</h3>
          <tr>
            <th>S/N</th>
            <th>Title</th>
            <th>No of Teachers</th>
            <th>Delete</th>
          </tr>
          <?php
            if ($type == 'all') {
              $subjects = $query->GetRowsFromTable($site.'.subjects', '', '', '', '', 'id');
              if (count($subjects)) {
                foreach ($subjects as $key => $value) {
                  $contents = $value['content'];
                  ?>
                    <tr class="view_subject" id="<?php echo $contents['id'] ?>">
                      <td><?php echo 1+$key?></td>
                      <td><?php echo $contents['title'] ?></td>
                      <td>
                        <a href="index.php?staff=show&type=teachers&subject=<?php echo $contents['id'] ?>">
                          <?php
                            $staff_no = $query->GetColumnFromTable($site.'.allocated_subjects', 'subject', $contents['id'], '', '', 'teacher');
                            echo count($staff_no);
                          ?>
                        </a>
                      </td>
                      <td>
                        <a href="index.php?subjects=delete&type=<?php echo $contents['id']; ?>">Delete</a>
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
      $subject = $query->GetFromTable($site.".subjects", 'id', $type, '', '', '');
      $allocated_subjects = $query->GetRowsFromTable($site.".allocated_subjects", 'subject', $type, '', '', 'id');
      ?>
      <div class="expandable">
        <div class="expandable_title">Bio-data<span class="toggle toggle_close"></span></div>
        <div class="expandable_content">
          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Title</h4>
              <div class="cell_content editable">
                <p><?php echo $subject['title'] ?></p>
                <input class="edit hide" type="text" name="" value="<?php echo $class['title'] ?>" data-table=".<?php echo "classes"; ?>" data-column="title"/>
              </div>
            </div>
            <a href="index.php?subjects=delete&type=<?php echo $type ?>" class="button_link right_align">Delete</a>
          </div>
        </div>
      </div>
      <br><br><br>
      <table class="table">
        <h3 class="title">Classes offering <?php echo $query->GetFromTable($site.".subjects", 'id', $type, '', '', 'title'); ?></h3>
        <tr>
          <th>S/N</th>
          <th>Class Section</th>
          <th>Teacher</th>
          <th>Remove</th>
        </tr>
        <?php
          foreach ($allocated_subjects as $key => $value) {
            $contents = $value['content'];
            ?>
            <tr class="view_student" id="<?php echo $contents['e_key']; ?>">
              <td><?php echo $key + 1 ?></td>
              <td><a href="index.php?class_sections=view&type=<?php echo $contents['class_section']; ?>"><?php echo $query->GetFromTable($site.'.classes', 'id', $query->GetFromTable($site.'.class_sections', 'id', $contents['class_section'], '', '', 'class'), '', '', 'title').' '.$query->GetFromTable($site.'.class_sections', 'id', $contents['class_section'], '', '', 'section'); ?></a></td>
              <td><a href="index.php?profile=staff&key=<?php echo $contents['teacher'] ?>&owner=staff"><?php echo $query->GetFromTable($site.".staff", "e_key", $contents['teacher'], '', '', 'user_name'); ?></a></td>
              <td><a href="index.php?subjects=de_allocate&type=<?php echo $contents['id']; ?>">Remove</a></td>
            </tr>
            <?php
          }
         ?>
      </table>
      <?php
    }
    elseif ($action == 'allocate') {
      ?>
      <form class="form" id="allocate_subject" method="post">
        <h3 class="title">Subject Allocation</h3>
        <div>
          <label for="class_section">Class Section</label>
          <select name="class_section" id="class_section">
            <option selected disabled value="0">Select Class Section</option>
            <?php
            $class_sections = $query->GetRowsFromTable($site.'.class_sections', '', '', '', '', 'id');
            foreach ($class_sections as $key => $value) {
              $contents = $value['content'];
              ?>
              <option value=<?php echo $value['id']; ?>><?php echo $query->GetFromTable($site.'.classes', 'id', $contents['class'], '', '', 'title').' '.$contents['section']; ?></option>
              <?php
            }
             ?>
          </select>
        </div>
        <div>
          <label for="teacher">Teacher</label>
          <select name="teacher" id="teacher">
            <option selected disabled value="0">Select Teacher</option>
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
          <button id="submit">Allocate subject</button>
        </div>
      </form>
      <?php
    }
    elseif ($action == 'de_allocate') {
      if ($query->DeleteFromDB($site.'.allocated_subjects', 'id', $type)) {
        $utk->Alert("De-allocation was successful");
        $utk->Redirect($_SESSION['referer']);
      }
    }
    elseif ($action == 'delete') {
      if ($query->DeleteFromDB($site.'.subjects', 'id', $type)) {
        if ($query->DoesRowsExist($site.'.allocated_subjects', ['subject'], [$type])) {
          if ($query->DeleteFromDB($site.'.allocated_subjects', 'subject', $type)) {
            $utk->Alert("Subject has been deleted");
            $utk->Redirect($_SESSION['referer']);
          }
        }else {
          $utk->Alert("Subject has been deleted");
          $utk->Redirect($_SESSION['referer']);
        }
      }
    }
 ?>
