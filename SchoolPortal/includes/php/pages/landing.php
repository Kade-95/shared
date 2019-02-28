<?php
  $owner = $user.'_session';
  if (isset($_SESSION[$owner])) {
    $owner = $_SESSION[$owner];
  }
  ?>
    <div class="landing">
      <?php echo $user ?>
    </div>
  <?php
?>
