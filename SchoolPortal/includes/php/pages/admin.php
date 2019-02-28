<?php
  $action = $type = '';
  if (isset($_GET['admin'])) {
    $action = $_GET['admin'];
  }
  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }
  ?>
  <div class="sub_menu">
    <a href="index.php?admin=add" class="button_link left_align">Add</a>
    <a href="index.php?admin=show&type=online" class="button_link left_align">Online</a>
    <a href="index.php?admin=show&type=offline" class="button_link left_align">OffLine</a>
  </div>
  <?php
  if ($action == 'add') {
    $admin = $query->GetRowsFromTable($site.'.staff', 'charge', '0', '', '', 'e_key');
    ?>
    <table class="table">
      <tr>
        <th>S/N</th>
        <th>Staff</th>
        <th>Make</th>
      </tr>
      <?php
        foreach ($admin as $key => $value) {
          $contents = $value['content'];
          ?>
          <tr>
            <td><?php echo 1 + $key ?></td>
            <td><?php echo $contents['user_name'] ?></td>
            <td><a href="index.php?admin=make&type=<?php echo $contents['e_key']; ?>">Make</a></td>
          </tr>
          <?php
        }
       ?>
    </table>
    <?php
  }
  elseif ($action ==  'make') {
    $sql = "UPDATE $site.staff SET charge = '1' WHERE e_key='$type'";
    if ($query->Insert($sql)) {
      $utk->Alert("Admin has been added");
      $utk->Redirect($_SESSION['referer']);
    }
  }
  elseif ($action == 'show') {
    $admin = $query->GetRowsFromTable($site.'.staff', 'charge', '1', '', '', 'e_key');
    if ($type == 'offline') {
      $admin = $query->GetRowsFromTable($site.'.staff', 'charge', '1', 'status', '0', 'e_key');
    }elseif ($type == 'online') {
      $admin = $query->GetRowsFromTable($site.'.staff', 'charge', '1', 'status', '1', 'e_key');
    }
    ?>
    <table class="table">
      <tr>
        <th>S/N</th>
        <th>Staff</th>
        <th>Availability</th>
        <th>Remove</th>
      </tr>
      <?php
        foreach ($admin as $key => $value) {
          $contents = $value['content'];
          ?>
          <tr>
            <td><?php echo 1 + $key ?></td>
            <td><?php echo $contents['user_name'] ?></td>
            <td><?php echo ($contents['status']) ? "Online":"Offline" ?></td>
            <td><a href="index.php?admin=remove&type=<?php echo $contents['e_key']; ?>">Remove</a></td>
          </tr>
          <?php
        }
       ?>
    </table>
    <?php
  }
  elseif ($action == 'remove') {
    $sql = "UPDATE $site.staff SET charge = '0' WHERE e_key='$type'";
    if ($query->Insert($sql)) {
      $utk->Alert("Admin has been removed");
      $utk->Redirect($_SESSION['referer']);
    }
  }
 ?>
