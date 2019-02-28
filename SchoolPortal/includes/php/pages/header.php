<?php
  $login = 1;
?>

<header class="display" data-worker="" data-session="<?php if(isset($_SESSION['session'])){echo $_SESSION['session'];}?>" data-type="<?php if(isset($_SESSION['type'])){echo $_SESSION['type'];}?>">
  <?php
    if($user == "user"){
      ?>
      <div id="sign_in_menu">
        <a class="button_link right_align" id="sign_in">Sign in</a>
        <ul id="sign_in_as" class="hide">
          <li><a href="index.php?sign_in=staff" id="sign_in_as_staff" class="button_link">As Staff</a>
          </li>
          <li><a href="index.php?sign_in=student" class="button_link" id="sign_in_as_student">As Student</a>
          </li>
        </ul>
      </div>
      <a></a>
      <?php
    }
    else{
      if (isset($_SESSION['session'])) {
        if ($session->IsUserExpired($_SESSION['session'])) {
          $login = 0;
          if ($user == 'student') {
            if ($query->GetFromTable('SIMS.student', 'email', $_SESSION['session'], '', '', 'autologin')) {
              $login = $persist->CheckCredentials();
            }
          }
        }
        if (!$login) {
          header('location: index.php?logout');
        }
        ?>
        <div class="main_menu">
          <?php
            if ($user == 'admin') {
              ?>

              <?php
            }
            elseif($user == "staff"){
              ?>

              <?php
            }
            elseif($user == "student"){
              ?>

              <?php
            }
          ?>
          <div>
            <span class="nav_toggle nav_open"></span>
            <a href="index.php?profile=<?php echo $user; ?>&key=<?php echo $_SESSION['session'] ?>&owner=<?php echo $_SESSION['type']; ?>" class="button_link right_align">Profile</a>
            <a href="index.php?logout" class="button_link right_align">Sign out</a>
          </div>
        </div>
        <?php
      }
    }
   ?>
</header>


<div class="nav_up">

</div>
