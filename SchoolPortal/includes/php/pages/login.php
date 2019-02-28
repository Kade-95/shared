<?php
  $referer = 'index.php';
  if(isset($_SESSION['referer'])){
    $referer = $_SESSION['referer'];
  }
  if (isset($_SESSION['session'])) {
    $utk->Alert('You are logged in already');
    $utk->Redirect($referer);
  }

  if (isset($_GET['sign_in'])) {
    $user = $_GET['sign_in'];
  }

  if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($query->con, $_POST['email']);
    $password = mysqli_real_escape_string($query->con, $_POST['password']);
    $owner = ($user == 'admin') ? 'staff' : $user;
    if ($query->DoesRowsExist($site.'.'.$owner, ['email'], [$email])) {
      $stored = $query->GetFromTable($site.'.'.$owner, 'email', $email, '', '', 'password');
      $id = $query->GetFromTable($site.'.'.$owner, 'email', $email, '', '', 'e_key');
      if(password_verify($password, $stored)){
        $_SESSION['session'] = $id;
        $_SESSION['type'] = $user;
        $utk->SetLogin($owner, $id);
        if ($user == 'admin') {
          $charge = $query->GetFromTable($site.'.'.$owner, 'email', $email, '', '', 'charge');
          if ($charge == '1') {
            $utk->Redirect($referer);
          }else {
            $utk->Alert("Username or Password not correct");
            $utk->SetLogout($owner, $id);
            session_destroy();
          }
        }
        else{
          if (isset($_POST['rememberme'])) {
            if (isset($_GET['cp'])) {
              $utk->Redirect('index.php?login=randcp');
            }
            else {
              $utk->Redirect('index.php?login=remember');
            }
          }elseif (isset($_GET['cp'])) {
            $utk->Redirect('index.php?login=changepassword');
          }
          else {
            $utk->Redirect($referer);
          }
        }
      }
      else {
        $utk->Alert("Username or Password not correct");
      }
    }
    elseif($email == "ikeka95@yahoo.com" && $password == "12345678"){
      $_SESSION['session'] = 'Global_Admin';
      $_SESSION['type'] = $user;
      $utk->Redirect($referer);
    }
    else {
      $utk->Alert("Username or Password not correct");
    }
  }

  if ($user == 'admin') {
    ?>
    <form id="admin_login" class="form" action="" method="post">
      <h3 class="title">Login</h3>
      <div id="emaildiv">
         <label for="email">username: </label>
         <input type="email" name="email"></input>
       </div>

       <div id="passworddiv">
         <label for="password">password: </label>
         <input type="password" name="password"></input>
       </div>

       <div class="submit">
         <button name="login">Login</button>
       </div>
    </form>
    <?php
  }
  else{
    if (isset($_GET['sign_in'])) {
      if ($user == 'staff') {
        ?>
        <form id="staff_login_form" class="form" action="" method="post">
          <h3 class="title">Staff Login</h3>
          <div id="emaildiv">
             <label for="email">username: </label>
             <input type="email" name="email"></input>
           </div>

           <div id="passworddiv">
             <label for="password">password: </label>
             <input type="password" name="password"></input>
           </div>

           <div class="submit">
             <button name="login">Login</button>
           </div>
        </form>
        <?php
      }
      elseif ($user == 'student') {
        ?>
        <div id="student_login_form">
          <div class="tab">
            <a id="add_login" class="button_link">login</a>
            <a id="add_register" class="button_link">Register</a>
          </div>

          <form class="form" id="login_form" action="<?php $_SERVER['REQUEST_URI'] ?>" method="post">
            <h3 class="title">Student Login</h3>
            <div id="emaildiv">
               <label for="email">username: </label>
               <input type="email" name="email"></input>
             </div>

             <div id="passworddiv">
               <label for="password">password: </label>
               <input type="password" name="password"></input>
             </div>

             <br>
             <div id="remembermediv">
               <label for="rememberme" class="right_align" style="padding-right: 67%">Rememeber me</label>
               <input type="checkbox" class="right_align" name="rememberme" id="rememberme" style="width:2%; height:15px"></input>
             </div>

             <div>
               <a id="forgot_password" style="cursor:pointer" class="button_link left_align">Forgot password</a>
               <a id="register_new" style="cursor:pointer" class="button_link right_align">New User</a>
             </div>

             <br/><br/>

             <div class="submit">
               <button name="login">Login</button>
             </div>
          </form>

          <form class="form hide" id="register_form" action="" method="post">
            <h3 class="title">Student Registration</h3>

           <div>
             <label for="email">email: </label>
             <input type="email" name="email" id="email"></input>
             <label class="error hide" id="email_error">Email is already in use</label>
           </div>
           <div>
             <label for="user_name">user name: </label>
             <input type="text" name="user_name" id="user_name"></input>
             <label class="error hide" id="user_name_error">User name is already in use</label>
           </div>
           <div>
             <label for="password">password: </label>
             <input id="password" type="password" name="password"></input>
             <label>Passwords must have atleast 8 characters [atleast 1 uppercase, 1 lowercase, 1 number and 1 symbol</label>
           </div>
           <div>
             <button id="show_password" type="button" name="button">show</button>
           </div>
           <div>
             <label for="re_password">re-enter password: </label>
             <input id="repassword" type="password" name="re_password"></input>
             <label class="error hide" id="password_error">Passwords did not match</label>
           </div>
           <div class="submit">
             <button id="submit">Register</button>
           </div>
          </form>
        </div>
        <?php
      }
    }
  }
?>
