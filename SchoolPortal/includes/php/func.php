<?php
    global $root, $ukt, $persist, $db, $man, $user, $session, $site, $online, $cd, $ignore_referer;
    $site = "SchoolPortal";
    $root = $_SERVER['DOCUMENT_ROOT']."/$site/";
    include_once $root."includes/php/autoload.php";
    $db = new classes\Database($site);
    $query = new classes\Query($db->con);
    $utk = new classes\UTK();
    $man = new classes\StringManipulator();
    $user = 'user';
    if ($man->IsSubString(getcwd(), 'admin')) {
        $user = 'admin';
        $cd = "../";
    }
    else {
        $cd = "";
    }

    $banner = $cd.'images/banner.png';
    $nav = $cd."images/nav.png";
    $close = $cd."images/close.png";
    $message = $cd."images/message.png";
    $no_preview = $cd."images/no_preview.png";
    $loader = $cd."images/loader.gif";

    $session = new classes\Sessions\MysqliSessionsHandler();
    if (!$utk->IgnoreReferer()) {
      $_SESSION['referer'] = $utk->GetURI();
    }

    if (isset($_SESSION['type'])) {
      $user = $_SESSION['type'];
    }

    $persist = new classes\Sessions\PersistentSessionsHandler();
    if(isset($_POST['register_student'])){
        $user_name = mysqli_real_escape_string($query->con, $_POST['user_name']);
        $email = mysqli_real_escape_string($query->con, $_POST['email']);
        $password = password_hash(mysqli_real_escape_string($query->con, $_POST['password']), PASSWORD_DEFAULT);
        $e_key = $query->GenerateUserKey($user_name, 'student');
        $com_code = md5(uniqid(rand()));

        $sql = "INSERT INTO $site.student (e_key, email, user_name, password, confirmed, com_code) VALUE('$e_key', '$email', '$user_name', '$password', '0', '$com_code')";
        if($query->Insert($sql)){
          //send confirmation mail to user email
          $to = $email;
          $subject = "Confirmation from fimblog to $user_name";
          $header = "From: cabgrab@gmail.com";
          $message = "Please click this below to verify and activate your account. rn";
          $message .= "http://www.SchoolPortal.online/work/projects/lost_and_found/customer/confirm.php?code=$com_code";

          $sendTo = mail($to, $subject, $message, $header);
          echo 1;
        }else {
          echo 0;
        }
    }

    if(isset($_POST['add_staff'])){
        $email = mysqli_real_escape_string($query->con, $_POST['email']);
        $open_date = mysqli_real_escape_string($query->con, $_POST['open_date']);
        $e_key = $query->GenerateUserKey($email, 'staff');
        $com_code = md5(uniqid(rand()));
        $password = password_hash('verified', PASSWORD_DEFAULT);

        $sql = "INSERT INTO $site.staff (e_key, email, password, charge, status, open_date, confirmed, com_code) VALUE('$e_key', '$email', '$password', '0', '0', '$open_date', '0', '$com_code')";
        if($query->Insert($sql)){
          //send confirmation mail to user email
          $to = $email;
          $subject = "Confirmation from fimblog to $email";
          $header = "From: cabgrab@gmail.com";
          $message = "Please click this below to verify and activate your account. rn";
          $message .= "http://www.SchoolPortal.online/work/projects/lost_and_found/customer/confirm.php?code=$com_code";

          $sendTo = mail($to, $subject, $message, $header);
          echo 1;
        }else {
          echo 0;
        }
    }

    if (isset($_POST['add_class'])) {
      $title = mysqli_real_escape_string($query->con, $_POST['title']);
      $sql = "INSERT INTO $site.classes(title) VALUES('$title')";
      echo $query->Insert($sql);
    }

    if (isset($_POST['add_class_section'])) {
      $section = mysqli_real_escape_string($query->con, $_POST['section']);
      $class = mysqli_real_escape_string($query->con, $_POST['class']);
      $form_teacher = mysqli_real_escape_string($query->con, $_POST['form_teacher']);
      $sql = "INSERT INTO $site.class_sections(section, class, form_teacher) VALUES('$section', '$class', '$form_teacher')";
      echo $query->Insert($sql);
    }

    if (isset($_POST['add_subject'])) {
      $title = mysqli_real_escape_string($query->con, $_POST['title']);
      $sql = "INSERT INTO $site.subjects(title) VALUES('$title')";
      echo $query->Insert($sql);
    }

    if (isset($_POST['allocate_subject'])) {
      $subject = mysqli_real_escape_string($query->con, $_POST['subject']);
      $teacher = mysqli_real_escape_string($query->con, $_POST['teacher']);
      $class_section = mysqli_real_escape_string($query->con, $_POST['class_section']);

      $sql = "INSERT INTO $site.allocated_subjects(subject, teacher, class_section) VALUES('$subject', '$teacher', '$class_section')";
      echo $query->Insert($sql);
    }

    if (isset($_POST['set_exam'])) {
      $subject = mysqli_real_escape_string($query->con, $_POST['subject']);
      $class = mysqli_real_escape_string($query->con, $_POST['class']);
      $academic_session = mysqli_real_escape_string($query->con, $_POST['academic_session']);
      $term = mysqli_real_escape_string($query->con, $_POST['term']);
      $type = mysqli_real_escape_string($query->con, $_POST['type']);
      $due = mysqli_real_escape_string($query->con, $_POST['due']);
      $qanda = json_decode($_POST['qanda']);

      $sql = "INSERT INTO $site.exams(subject, class, academic_session, term, type, due) VALUES('$subject', '$class', '$academic_session', '$term', '$type', '$due')";
      if ($query->Insert($sql)) {
        $last_id = $query->GetColumnFromTable($site.'.exams', '', '', '', '', 'id');
        $last_id = $last_id[count($last_id)-1];
        foreach ($qanda as $key => $value) {
          $sql = "INSERT INTO $site.qanda(owner, question, question_number, a, b, c, d, correct) VALUES('$last_id', '$value->question', '$value->question_number', '$value->a', '$value->b', '$value->c', '$value->d', '$value->correct')";
          $query->Insert($sql);
        }
        echo 1;
      }else {
        echo 0;
      }
    }

    if (isset($_POST['edit_exam'])) {
      $id = mysqli_real_escape_string($query->con, $_POST['id']);
      $subject = mysqli_real_escape_string($query->con, $_POST['subject']);
      $class = mysqli_real_escape_string($query->con, $_POST['class']);
      $academic_session = mysqli_real_escape_string($query->con, $_POST['academic_session']);
      $term = mysqli_real_escape_string($query->con, $_POST['term']);
      $type = mysqli_real_escape_string($query->con, $_POST['type']);
      $due = mysqli_real_escape_string($query->con, $_POST['due']);

      $sql = "UPDATE $site.exams SET subject='$subject', class='$class', academic_session='$academic_session', term='$term', type='$type', due='$due' WHERE id='$id'";
      echo $query->Insert($sql);
    }

    if (isset($_POST['add_new_question'])) {
      $exam = mysqli_real_escape_string($query->con, $_POST['owner']);
      $question = mysqli_real_escape_string($query->con, $_POST['question']);
      $question_number = mysqli_real_escape_string($query->con, $_POST['question_number']);
      $a = mysqli_real_escape_string($query->con, $_POST['a']);
      $b = mysqli_real_escape_string($query->con, $_POST['b']);
      $c = mysqli_real_escape_string($query->con, $_POST['c']);
      $d = mysqli_real_escape_string($query->con, $_POST['d']);
      $correct = mysqli_real_escape_string($query->con, $_POST['correct']);

      $sql = "INSERT INTO $site.qanda(owner, question, question_number, a, b, c, d, correct) VALUES('$exam', '$question', '$question_number', '$a', '$', '$c', '$d', '$correct')";
      echo $query->Insert($sql);
    }

    if (isset($_POST['edit_qanda'])) {
      $id = mysqli_real_escape_string($query->con, $_POST['id']);
      $question = mysqli_real_escape_string($query->con, $_POST['question']);
      $question_number = mysqli_real_escape_string($query->con, $_POST['question_number']);
      $a = mysqli_real_escape_string($query->con, $_POST['a']);
      $b = mysqli_real_escape_string($query->con, $_POST['b']);
      $c = mysqli_real_escape_string($query->con, $_POST['c']);
      $d = mysqli_real_escape_string($query->con, $_POST['d']);
      $correct = mysqli_real_escape_string($query->con, $_POST['correct']);

      $sql = "UPDATE $site.qanda SET question='$question', correct='$correct', a='$a', b='$b', c='$c', d='$d' WHERE id='$id'";
      echo $query->Insert($sql);
    }

    if (isset($_POST['submit_exam'])) {
      $exam_session = mysqli_real_escape_string($query->con, $_POST['exam_session']);
      $exam = mysqli_real_escape_string($query->con, $_POST['exam']);
      $owner = mysqli_real_escape_string($query->con, $_POST['owner']);
      $qanda = $_POST['qanda'];
      $score = $query->MarkExam($exam, json_decode($qanda));
      $sql = "INSERT INTO $site.scores(owner, exam, score, qanda) VALUES('$owner', '$exam', '$score', '$qanda')";
      if ($query->Insert($sql)) {
        echo $query->DeleteFromDB($site.'.exam_sessions', 'id', $exam_session);
      }
      else {
        echo "0";
      }
    }

    if (isset($_POST['store_exam_session'])) {
      $id = mysqli_real_escape_string($query->con, $_POST['id']);
      $remaining_time = mysqli_real_escape_string($query->con, $_POST['remaining_time']);
      $qanda = $_POST['qanda'];
      $sql = "UPDATE $site.exam_sessions SET remaining_time='$remaining_time', qanda='$qanda' WHERE id='$id'";
      echo $query->Insert($sql);
    }

    if (isset($_POST['publish_result'])) {
      $academic_session = mysqli_real_escape_string($query->con, $_POST['academic_session']);
      $term = mysqli_real_escape_string($query->con, $_POST['term']);

      $results = $query->GetResults($academic_session, $term);

      foreach ($results as $key => $value) {
        $types = $value['type'];
        foreach ($types as $t => $type) {
          echo $type['type'];
        }
      }
      return;
      $sql = "UPDATE $site.exam_sessions SET remaining_time='$remaining_time', qanda='$qanda' WHERE id='$id'";
      echo $query->Insert($sql);
    }

    if (isset($_POST['make_admin'])) {
      $owner = mysqli_real_escape_string($query->con, $_POST['make_admin']);
      $e_key = mysqli_real_escape_string($query->con, $_POST['e_key']);
      $sql = "UPDATE $site.$owner SET charge='1' WHERE e_key='$e_key'";
      echo mysqli_query($query->con, $sql);
    }

    if (isset($_POST['remove_admin'])) {
      $owner = mysqli_real_escape_string($query->con, $_POST['remove_admin']);
      $e_key = mysqli_real_escape_string($query->con, $_POST['e_key']);
      $sql = "UPDATE $site.$owner SET charge='0' WHERE e_key='$e_key'";
      echo mysqli_query($query->con, $sql);
    }


    if(isset($_POST['getfromtable'])) {
        $table = $site.mysqli_real_escape_string($query->con, $_POST['getfromtable']);
        $col_name1 = mysqli_real_escape_string($query->con, $_POST['colname1']);
        $col_name2 = mysqli_real_escape_string($query->con, $_POST['colname2']);
        $col_value1 = mysqli_real_escape_string($query->con, $_POST['colvalue1']);
        $col_value2 = mysqli_real_escape_string($query->con, $_POST['colvalue2']);
        $col_return = mysqli_real_escape_string($query->con, $_POST['colreturn']);
        echo json_encode($query->GetFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $col_return), JSON_PRETTY_PRINT);
    }

    if (isset($_POST['getcolumnfromtable'])) {
        $table = $site.mysqli_real_escape_string($query->con, $_POST['getcolumnfromtable']);
        $col_name1 = mysqli_real_escape_string($query->con, $_POST['colname1']);
        $col_name2 = mysqli_real_escape_string($query->con, $_POST['colname2']);
        $col_value1 = mysqli_real_escape_string($query->con, $_POST['colvalue1']);
        $col_value2 = mysqli_real_escape_string($query->con, $_POST['colvalue2']);
        $col_return = mysqli_real_escape_string($query->con, $_POST['colreturn']);
        echo json_encode($query->GetColumnFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $col_return), JSON_PRETTY_PRINT);
    }

    if (isset($_POST['getrowsfromtable'])) {
        $table = $site.mysqli_real_escape_string($query->con, $_POST['getrowsfromtable']);
        $col_name1 = mysqli_real_escape_string($query->con, $_POST['colname1']);
        $col_name2 = mysqli_real_escape_string($query->con, $_POST['colname2']);
        $col_value1 = mysqli_real_escape_string($query->con, $_POST['colvalue1']);
        $col_value2 = mysqli_real_escape_string($query->con, $_POST['colvalue2']);
        $id = mysqli_real_escape_string($query->con, $_POST['id']);
        echo json_encode($query->GetRowsFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $id), JSON_PRETTY_PRINT);
    }

    if (isset($_POST['doesrowsexist'])) {
        $table = $site.$_POST['doesrowsexist'];
        $colnames = explode(',', $_POST['colnames']);
        $colvalues = explode(',', $_POST['colvalues']);
        echo $query->DoesRowsExist($table, $colnames, $colvalues);
    }

    if (isset($_POST['deletefromdb'])) {
        $table = $site.$_POST['deletefromdb'];
        $column = $_POST['column'];
        $group = $_POST['group'];
        echo $query->DeleteFromDB($table, $column, $group);
    }

    if (isset($_POST['insertintodb'])) {
        $sql = $_POST['insertintodb'];
        $table = $site.$_POST['table'];
        $sql = $man->StringReplace($sql, '***', $table);
        echo $query->Insert($sql);
    }

    if (isset($_POST['logout'])) {
      $persist->Logout(false);
    }

    if (isset($_POST['getRoot'])) {
        echo $root;
    }

    if (isset($_POST['isFolder'])) {
        if (is_dir($_POST['isFolder'])) {
        echo 'folder';
        }elseif (is_file($_POST['isFolder'])) {
        echo "file";
        }else {
        echo "null";
        }
    }
?>
