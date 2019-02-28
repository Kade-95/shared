<?php
  $action = '';
  $type = '';

  if (isset($_GET['exams'])) {
    $action = $_GET['exams'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  if ($action == 'show') {
    $student = $_SESSION['session'];
    $class = $query->GetFromTable($site.'.student', 'e_key', $student, '', '', 'class');
    $exams = $query->GetRowsFromTable($site.'.exams', 'class', $class, '', '', 'id');
    ?>
    <br><br>
    <div class="">
      <a href="index.php?exams=show&type=not_taken" class="button_link left_align">Not Taken</a>
      <a href="index.php?exams=show&type=not_taken" class="button_link left_align">Taken</a>
    </div>
    <br><br><br><br><br>
    <table class="table">
      <h2 class="title">My Exams</h2>
      <tr>
        <th>S/N</th>
        <th>Subject</th>
        <th>Class</th>
        <th>Academic Session</th>
        <th>Term</th>
        <th>Type</th>
        <th>Due</th>
        <th>Taken</th>
      </tr>
      <?php
        foreach ($exams as $key => $value) {
          $contents = $value['content'];
          // if ($query->DoesRowsExist($site.'.scores', ['owner', 'exam'], [$student, $contents['id']])) {
          //   continue;
          // }
          ?>
          <tr class="<?php echo ($query->DoesRowsExist($site.'.scores', ['owner', 'exam'], [$student, $contents['id']])) ? 'marked_exam':'take_exam'?>" id="<?php echo $contents['id'] ?>">
            <td><?php echo $key + 1; ?></td>
            <td><?php echo $query->GetFromTable($site.'.subjects', 'id', $contents['subject'], '', '', 'title'); ?></td>
            <td><?php echo $query->GetFromTable($site.'.classes', 'id', $contents['class'], '', '', 'title'); ?></td>
            <td><?php echo $contents['academic_session'] ?></td>
            <td><?php echo $utk->FetchTerms()[$contents['term']] ?></td>
            <td><?php echo $utk->FetchExamTypes()[$contents['type']] ?></td>
            <td><?php echo $contents['due'] ?></td>
            <td><?php echo ($query->DoesRowsExist($site.'.scores', ['owner', 'exam'], [$student, $contents['id']])) ? "Yes":"No"?></td>
          </tr>
          <?php
        }
       ?>
    </table>
    <?php
  }
  if ($action == 'take') {
    if ($query->DoesRowsExist($site.'.scores', ['owner', 'exam'], [$_SESSION['session'], $type])) {
      echo "You have taken this exam before. You are not allowed here please.";
      return;
    }
    $id = '';
    $exam = $query->GetFromTable($site.'.exams', 'id', $type, '', '', '');
    $questions = $query->GetRowsFromTable($site.'.qanda', 'owner', $type, '', '', 'id');
    $time = count($questions);
    $minutes = ($time > 1)? 'minutes':'minute';
    $min = '';
    $sec = '';
    $remaining = $time*60;

    $previous_session = $query->GetFromTable($site.'.exam_sessions', 'owner', $_SESSION['session'], 'exam', $type, '');
    if (!count($previous_session)) {
      //create new Exam session
      $owner = $_SESSION['session'];
      $sql = "INSERT INTO $site.exam_sessions(owner, exam, remaining_time) VALUES('$owner', '$type', '$remaining')";
      $query->Insert($sql);
      $id = $query->GetLastRowId($site.'.exam_sessions', 'id');
    }else {
      $id = $previous_session['id'];
      $remaining = $previous_session['remaining_time'];
    }

    ?>
    <br>
    <div class="right_align" id="remaining_time" data-exam_session=<?php echo $id; ?> data-time=<?php echo $remaining; ?>>
      <h2 class="button_link"><?php echo "<a id='min'>00</a>".':'. "<a id='sec'>00</a>" .' '."<a id='minutes'>$minutes</a> Left" ?></h2>
    </div>
    <br><br><br><br><br>
    <div class="">
      <h2 class="title"><?php echo $query->GetFromTable($site.'.subjects', 'id', $exam['subject'], '', '', 'title').' '.$utk->FetchExamTypes()[$exam['type']].'<br>'.$query->GetFromTable($site.'.classes', 'id', $exam['class'], '', '', 'title').'<br>'.$exam['academic_session'].' academic session '.$utk->FetchTerms()[$exam['term']] ?></h2>
      <br><br>
      <div id="exam_paper" data-session="<?php echo $id ?>">
        <?php
          foreach ($questions as $key => $value) {
            $contents = $value['content'];
            ?>
            <div class="question left_align" data-number='<?php echo $contents['question_number'] ?>'>
              <div class="">
                <p><?php echo 'Q'.$contents['question_number'] .". ". $contents['question'] ?></p>
              </div><br>
              <?php
                if (count($previous_session)) {
                  ?>
                  <div class="">
                    <?php
                      $qanda = json_decode($previous_session['qanda']);
                      foreach ($utk->FetAnswerOptions() as $option_key => $option) {
                        if ($qanda[$key]->question_number == $contents['question_number'] && $qanda[$key]->answer == $option) {
                          ?>
                          <a class="answer"><input type="checkbox" checked data-name='<?php echo $option ?>'><?php echo $option.'.'.$contents[$man->ToLowerCase($option)] ?></a>
                          <?php
                        }else {
                          ?>
                          <a class="answer"><input type="checkbox" data-name='<?php echo $option ?>'><?php echo $option.'.'.$contents[$man->ToLowerCase($option)] ?></a>
                          <?php
                        }
                      }
                     ?>
                  </div>
                  <?php
                }else {
                  ?>
                  <div class="">
                    <?php
                    foreach ($utk->FetAnswerOptions() as $key => $value) {
                      ?>
                      <a class="answer"><input type="checkbox" data-name='<?php echo $value ?>'><?php echo $value.'.'.$contents[$man->ToLowerCase($value)] ?></a>
                      <?php
                    }
                     ?>
                  </div>
                  <?php
                }
               ?>
            </div>
            <?php
          }
         ?>
         <div class="submit">
           <button id="submit">Submit</button>
         </div>
      </div>
    </div>
    <?php
  }
  if ($action == 'marked') {
    $questions = $query->GetRowsFromTable($site.'.qanda', 'owner', $type, '', '', 'id');
    $exam = $query->GetFromTable($site.'.exams', 'id', $type, '', '', '');
    $scores = $query->GetFromTable($site.'.scores', 'exam', $type, 'owner', $_SESSION['session'], '');
    ?>
    <br>
    <div class="right_align" id="remaining_time">
      <h2 class="button_link"><?php echo "Score: ".round($scores['score'], 2).'%' ?></h2>
    </div>
    <br><br><br><br><br>
    <div class="">
      <h2 class="title"><?php echo $query->GetFromTable($site.'.subjects', 'id', $exam['subject'], '', '', 'title').' '.$utk->FetchExamTypes()[$exam['type']].'<br>'.$query->GetFromTable($site.'.classes', 'id', $exam['class'], '', '', 'title').'<br>'.$exam['academic_session'].' academic session '.$utk->FetchTerms()[$exam['term']] ?></h2>
      <br><br>
      <div id="exam_paper" data-session="<?php echo $id ?>">
        <?php
          foreach ($questions as $key => $value) {
            $contents = $value['content'];
            ?>
            <div class="question left_align" data-number='<?php echo $contents['question_number'] ?>'>
              <div class="">
                <p><?php echo 'Q'.$contents['question_number'] .". ". $contents['question'] ?></p>
              </div><br>
              <div class="">
                <?php
                $qanda = json_decode($scores['qanda']);
                foreach ($utk->FetAnswerOptions() as $option_key => $option) {
                  if ($qanda[$key]->question_number == $contents['question_number'] && $qanda[$key]->answer == $option) {
                    ?>
                    <a class="answer" style="background: <?php echo ($option == $contents['correct']) ? 'red':''; ?>"><input type="checkbox" checked data-name='<?php echo $option ?>'><?php echo $option.'.'.$contents[$man->ToLowerCase($option)] ?></a>
                    <?php
                  }else {
                    ?>
                    <a class="answer" style="background: <?php echo ($option == $contents['correct']) ? 'red':''; ?>"><input type="checkbox" data-name='<?php echo $option ?>'><?php echo $option.'.'.$contents[$man->ToLowerCase($option)] ?></a>
                    <?php
                  }
                }
                 ?>
              </div>
            </div>
            <?php
          }
         ?>
      </div>
    </div>
    <?php
  }
 ?>
