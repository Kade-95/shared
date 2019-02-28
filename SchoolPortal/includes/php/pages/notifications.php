<?php
  $action = $_GET['notifications'];
  if ($action == 'viewall') {
    ?>
    <table class="table">
      <h3 class="title">All notifications</h3>
      <tr>
        <th>S/N</th>
        <th>Subject</th>
        <th>Date</th>
      </tr>

      <?php
      $all_notifications = $query->GetRowsFromTable($site.'.notification', 'user');
      foreach ($all_notifications as $key => $value) {
        $content = $value['content'];
        if ($content['user'] != $_SESSION['user_session']) {
          continue;
        }
      ?>
          <tr class="see_notification" id="<?php echo $content['note_key'] ?>" style="font-weight: <?php echo ($content['status']) ? '':'bold' ?>">
            <td><?php echo $key+1 ?></td>
            <td><?php echo $content['subject'] ?></td>
            <td><?php echo $content['notification_date'] ?></td>
          </tr>
        <?php
      }
     ?>
    </table>
    <?php
  }
  elseif ($action == 'view') {
    $id = $_GET['name'];
    $content = $query->GetFromTable($site.".notification", "note_key", $id, '', '', '');
    ?>
      <div class="title">
        <h3><?php echo $content['subject']; ?></h3>
      </div>
      <div class="message">
        <p><?php echo $content['message'].'<br><br>'.$content['notification_date'] ?></p>
      </div>
    <?php
    $sql = "UPDATE $site.notification SET status=1 WHERE note_key=".$content['note_key'];
    mysqli_query($query->con, $sql);
  }
 ?>
