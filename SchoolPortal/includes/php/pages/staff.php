<?php
  $action = ''; $type = '';

  ?>
    <div class="sub_menu">
      <a class="button_link left_align" href="index.php?staff=add">Add New</a>
      <a class="button_link left_align" href="index.php?staff=show&type=active">Active</a>
      <a class="button_link left_align" href="index.php?staff=show&type=retired">Retired</a>
      <?php
        if ($_SESSION['session'] == 'Global_Admin') {
          ?>
          <a class="button_link left_align" href="index.php?admin=show&type=staff">Manage Admins</a>
          <?php
        }
       ?>
    </div>
  <?php

    if (isset($_GET['staff'])) {
      $action = $_GET['staff'];
    }

    if (isset($_GET['type'])) {
      $type = $_GET['type'];
    }

    if ($action == 'add') {
      ?>
      <form class="form" id="add_staff" action="" method="post">
        <div>
          <label for="email">Email</label>
          <input type="email" name="email" id="email"></input>
        </div>
        <div>
          <label for="open_date">Start date:</label>
          <input type="date" name="open_date" id="open_date"></input>
        </div>
        <div class="submit">
          <button id="submit">Add staff</button>
        </div>
      </form>
      <?php
    }
    elseif ($action == 'show') {
      ?>
      <div>
        <table class="table">
          <h3 class="title"><?php echo $type ?> Staff</h3>
          <tr>
            <th>S/N</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Image</th>
            <th>Active</th>
            <th>Retire</th>
            <th>Delete</th>
          </tr>
          <?php
            $staff = $query->GetRowsFromTable($site.'.staff', '', '', '', '', 'e_key');
            if ($type == 'retired') {
              $staff = $query->GetRowsFromTable($site.'.staff', 'retired', '1', '', '', 'e_key');
            }elseif ($type == 'active') {
              $staff = $query->GetRowsFromTable($site.'.staff', 'retired', '0', '', '', 'e_key');
            }
            foreach ($staff as $key => $value) {
              $contents = $value['content'];
              ?>
                <tr class="view_staff" id="<?php echo $contents['e_key'] ?>">
                  <td><?php echo 1+$key?></td>
                  <td><?php echo $contents['full_name'] ?></td>
                  <td><?php echo $contents['email'] ?></td>
                  <td><?php echo $contents['image'] ?></td>
                  <td><?php echo ($contents['retired']) ? 'No':'Yes'; ?></td>
                  <td>
                    <a href="index.php?staff=<?php echo ($contents['retired']) ? 'activate':'retire'; ?>&type=<?php echo $contents['e_key']; ?>"><?php echo ($contents['retired']) ? 'Activate':'Retire'; ?></a>
                  </td>
                  <td>
                    <a href="index.php?staff=delete&type=<?php echo $contents['e_key']; ?>">Delete</a>
                  </td>
                </tr>
              <?php
            }
           ?>
        </table>
      </div>
      <?php
    }
    elseif ($action == 'retire') {
      $sql = "UPDATE $site.staff SET retired = '1' WHERE e_key='$type'";
      if ($query->Insert($sql)) {
        $utk->Alert("Staff has been retired");
        $utk->Redirect($_SESSION['referer']);
      }
    }
    elseif ($action == 'activate') {
      $sql = "UPDATE $site.staff SET retired = '0' WHERE e_key='$type'";
      if ($query->Insert($sql)) {
        $utk->Alert("Staff has been activated");
        $utk->Redirect($_SESSION['referer']);
      }
    }
    elseif ($action == 'delete') {
      if ($query->DeleteFromDB($site.'.staff', 'e_key', $type)) {
        $utk->Alert("Staff has been deleted");
        $utk->Redirect($_SESSION['referer']);
      }
    }
 ?>
