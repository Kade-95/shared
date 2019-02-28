<?php
?>
<div class="main_display">
  <h3 id="alert" class="hide"></h3>
  <div id="main_window" class="display">
    <?php
      if (isset($_GET['sign_in'])) {
        include $root.'includes/php/pages/login.php';
      }
      elseif($user == "user"){
        ?>
        <!-- <div id="banner">

        </div>
        <div id="qoutes">
          <p>Each successful step in life means there is a successful future ahead of us. No matter how the goings are tough, always remember that it takes the tough to keep on Going</p>
          <p>Don’t give up! You never lose faith! You got to keep believing! Remember who is behind you, remember who loves you and always will! Love you!</p>
          <p>Nothing is ever called Welcome back without a Journey. If you desire to get results, you work it out, rather than hoping and wishing. Hope you understand this my friend.</p>
        </div>
        <div id="motor">

        </div>
        <div id="offers">

        </div> -->
        <?php
      }
      else{
        ?>

        <div id="side_bar">
          <span class="nav_toggle nav_open"></span>
          <div id="panels">
            <ul>
              <?php
              if ($user == 'admin') {
                ?>
                  <li><a href="index.php?staff=show&type=all">Staff</a></li>
                  <li><a href="index.php?students=show&type=all">Students</a></li>
                  <li><a href="index.php?classes=show&type=all">Classes</a></li>
                  <li><a href="index.php?subjects=show&type=all">Subjects</a></li>
                  <li><a href="index.php?exams=show&type=all">Exams</a></li>
                  <li><a href="index.php?results=show&type=all">Results</a></li>
                <?php
              }elseif ($user == 'staff') {
                // code...
              }elseif ($user == 'student') {
                ?>
                  <li><a href="index.php?subjects=show&type=all">Subjects</a></li>
                  <li><a href="index.php?exams=show&type=all">Exams</a></li>
                  <li><a href="index.php?results=show&type=all">Results</a></li>
                <?php
              }
               ?>
            </ul>
          </div>
        </div>

        <?php
        if (isset($_SESSION['session'])) {
          if (isset($_GET['logout'])) {
            $owner = ($user == 'admin') ? 'staff' : $user;
            $id = $_SESSION['session'];
            $utk->SetLogout($owner, $id);
            $persist->Logout(false);
            if ($_GET['logout'] == 'changepassword') {
              header('location: index.php?cp');
            }
            else {
              header('location: index.php');
            }
          }
          elseif (isset($_GET['login'])) {
            if ($_GET['login'] == 'remember') {
              //$persist->MakePersistent();
              //header('location: index.php?login');
            }
            elseif ($_GET['login'] == 'randcp') {
              $persist->MakePersistent();
              header('location: index.php?changepassword');
            }
            elseif ($_GET['login'] == 'changepassword') {
              header('location: index.php?changepassword');
            }
            else {
              header('location: index.php');
            }
          }
          elseif (isset($_GET['changepassword'])) {
            include $root.'includes/php/pages/database/changepassword.php';
          }
          elseif (isset($_GET['profile'])) {
            include $root.'includes/php/pages/profile.php';
          }
          elseif (isset($_GET['staff'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/staff.php';
            }
          }
          elseif (isset($_GET['students'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/students.php';
            }
          }
          elseif (isset($_GET['classes'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/classes.php';
            }
          }
          elseif (isset($_GET['class_sections'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/class_sections.php';
            }
          }
          elseif (isset($_GET['subjects'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/subjects.php';
            }
          }
          elseif (isset($_GET['admin'])) {
            if ($_SESSION['session'] == 'Global_Admin') {
              include $root.'includes/php/pages/admin.php';
            }
          }
          elseif (isset($_GET['exams'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/exams.php';
            }
            elseif ($user == 'student') {
              include $root.'student/exams.php';
            }
          }
          elseif (isset($_GET['results'])) {
            if ($user == 'admin') {
              include $root.'includes/php/pages/results.php';
            }
            elseif ($user == 'student') {
              include $root.'student/results.php';
            }
          }
          else {
            include $root.'includes/php/pages/landing.php';
          }
        }
        else {
          if ($persist->CheckCredentials()) {
            header('location: index.php?login');
          }
          else {
            include $root.'includes/php/pages/login.php';
          }
        }
      }
      ?>
  </div>
</div>
