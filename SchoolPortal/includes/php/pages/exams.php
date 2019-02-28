<?php
  $action = ''; $type = ''; $correct_answers = ['A', 'B', 'C', 'D'];
  if (isset($_GET['exams'])) {
    $action = $_GET['exams'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  ?>
    <div class="sub_menu" data-class=<?php echo $type; ?>>
      <?php
        if ($action == 'view') {
          ?>
          <?php
        }else {
          ?>
          <a class="button_link left_align" href="index.php?exams=set">Set An Exam</a>
          <?php
        }
       ?>
    </div>
  <?php

    if ($action == 'set') {
      ?>
      <form class="form" id="set_exam" action="" method="post">
        <div>
          <label for="subject">Subject</label>
          <select name="subject" id="subject">
            <option selected disabled>Select Subject</option>
            <?php
              $subjects = $query->GetRowsFromTable($site.".subjects", '', '', '', '', 'id');
              foreach ($subjects as $key => $value) {
                $contents = $value['content'];
                ?>
                  <option value="<?php echo $contents['id']; ?>"><?php echo $contents['title']; ?></option>
                <?php
              }
             ?>
          </select>
        </div>
        <div>
          <label for="class">Class</label>
          <select name="class" id="class">
            <option selected disabled>Select Class</option>
            <?php
              $classes = $query->GetRowsFromTable($site.".classes", '', '', '', '', 'id');
              foreach ($classes as $key => $value) {
                $contents = $value['content'];
                ?>
                  <option value="<?php echo $contents['id']; ?>"><?php echo $contents['title']; ?></option>
                <?php
              }
             ?>
          </select>
        </div>
        <div>
          <label for="academic_session">Academic Session</label>
          <select name="academic_session" id="academic_session">
            <?php
              $academic_sessions = $utk->FetchAcademicSession(10);
              foreach ($academic_sessions as $key => $value) {
                if (!$key) {
                  ?>
                    <option selected disabled value="<?php echo $value ?>"><?php echo $value ?></option>
                  <?php
                }else {
                  ?>
                    <option value="<?php echo $value ?>"><?php echo $value ?></option>
                  <?php
                }
              }
             ?>
          </select>
        </div>
        <div>
          <label for="term">Term</label>
          <select name="term" id="term">
              <?php
                $terms = $utk->FetchTerms();
                foreach ($terms as $key => $value) {
                  if (!$key) {
                    ?>
                      <option selected disabled value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php
                  }else {
                    ?>
                      <option value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php
                  }
                }
               ?>
          </select>
        </div>
        <div>
          <label for="Type">Exam Type</label>
          <select name="Type" id="type">
            <?php
              $exam_types = $utk->FetchExamTypes();
              foreach ($exam_types as $key => $value) {
                if (!$key) {
                  ?>
                    <option selected disabled value="<?php echo $key ?>"><?php echo $value ?></option>
                  <?php
                }else {
                  ?>
                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                  <?php
                }
              }
             ?>
          </select>
        </div>
        <div>
          <label for="due">Exam Due</label>
          <input type="date" name="due" id="due"></input>
        </div>
        <div class="expandable" id="questions">
          <h1 class="expandable_title">Questions<span class="toggle toggle_close"></span></h1>
          <div class="expandable_content">
            <div class="a_question hide expandable" id="content_prototype">
              <h1 class="expandable_title">Question No.<span class="no"></span> <span class="close close_big remove_question"></span> <span class="toggle toggle_close"></span></h1><br><br>
              <div class="expandable_content">
                <textarea class="question" name="name" rows="8" cols="80"></textarea>
                <div class="answers">
                  <label for="a">A</label>
                  <input type="text" class="a" name="a" placeholder="A"><br>
                  <label for="b">B</label>
                  <input type="text" class="b" name="b" placeholder="B"><br>
                  <label for="c">C</label>
                  <input type="text" class="c" name="c" placeholder="C"><br>
                  <label for="d">D</label>
                  <input type="text" class="d" name="d" placeholder="D"><br>
                </div>
                <div>
                  <label for="correct">Correct Answer</label>
                  <select name="correct" class="correct">
                    <option selected disabled>Select Correct Answer</option>
                    <?php
                      foreach ($correct_answers as $key => $value) {
                        ?>
                        <option value="<?php echo $value ?>"><?php echo $value ?></option>
                        <?php
                      }
                     ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="submit">
          <button type="button" id="add_question">Add Question</button>
          <button id="submit">Set Exam</button>
        </div>
      </form>
      <?php
    }
    elseif ($action == 'show') {
      ?>
      <div>
        <table class="table">
          <h3 class="title"><?php echo $type ?> Exams</h3>
          <tr>
            <th>S/N</th>
            <th>Subject</th>
            <th>Class</th>
            <th>Academic Session</th>
            <th>Term</th>
            <th>Type</th>
            <th>Due</th>
            <th>Delete</th>
          </tr>
          <?php
            if ($type == 'all') {
              $exams = $query->GetRowsFromTable($site.'.exams', '', '', '', '', 'id');
              if (count($exams)) {
                foreach ($exams as $key => $value) {
                  $contents = $value['content'];
                  ?>
                    <tr class="view_exam" id="<?php echo $contents['id'] ?>">
                      <td><?php echo 1+$key?></td>
                      <td><?php echo $query->GetFromTable($site.'.subjects', 'id', $contents['subject'], '', '', 'title') ?></td>
                      <td>
                        <a href="index.php?classes=view&type=<?php echo $contents['class'] ?>">
                          <?php
                            echo $query->GetFromTable($site.'.classes', 'id', $contents['class'], '', '', 'title');
                          ?>
                        </a>
                      </td>
                      <td><?php echo $contents['academic_session']?></td>
                      <td><?php echo $utk->FetchTerms()[$contents['term']]?></td>
                      <td><?php echo $utk->FetchExamTypes()[$contents['type']]?></td>
                      <td><?php echo $contents['due']?></td>
                      <td>
                        <a href="index.php?exams=delete&type=<?php echo $contents['id']; ?>">Delete</a>
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
      $exams = $query->GetFromTable($site.".exams", '', '', '', '', '');
      $qanda = $query->GetRowsFromTable($site.".qanda", 'owner', $type, '', '', 'id');
      ?>
      <div class="expandable">
        <div class="expandable_title">About<span class="toggle toggle_close"></span></div>
        <div class="expandable_content" id="exam">
          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Subject</h4>
              <div class="cell_content editable">
                <select class="edit borderless" data-table=".exams" id="subject">
                  <?php
                    $subjects = $query->GetRowsFromTable($site.'.subjects', '', '', '', '', 'id');
                    foreach ($subjects as $key => $value) {
                      $contents = $value['content'];
                      if ($exams['subject'] == $contents['id']) {
                        ?>
                        <option selected value="<?php echo $contents['id'] ?>"><?php echo $contents['title'] ?></option>
                        <?php
                      }else {
                        ?>
                        <option value="<?php echo $contents['id'] ?>"><?php echo $contents['title'] ?></option>
                        <?php
                      }
                    }
                    ?>
                </select>
              </div>
            </div>
            <div class="cell right">
              <h4 class="cell_title">Class</h4>
              <div class="cell_content editable">
                <select class="edit borderless" data-table=".exams" id="class">
                  <?php
                    $classes = $query->GetRowsFromTable($site.'.classes', '', '', '', '', 'id');
                    foreach ($classes as $key => $value) {
                      $contents = $value['content'];
                      if ($exams['class'] == $contents['id']) {
                        ?>
                        <option selected value="<?php echo $contents['id'] ?>"><?php echo $contents['title'] ?></option>
                        <?php
                      }else {
                        ?>
                        <option value="<?php echo $contents['id'] ?>"><?php echo $contents['title'] ?></option>
                        <?php
                      }
                    }
                    ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Academic Session</h4>
              <div class="cell_content editable">
                <select class="edit borderless" data-table=".exams" id="academic_session">
                  <?php
                    $academic_sessions = $utk->FetchAcademicSession(10);
                    foreach ($academic_sessions as $key => $value) {
                      if ($key == 0) {
                        ?>
                        <?php
                      }else {
                        if ($exams['academic_session'] == $value) {
                          ?>
                          <option selected value="<?php echo $value ?>"><?php echo $value?></option>
                          <?php
                        }else {
                          ?>
                          <option value="<?php echo $value ?>"><?php echo $value?></option>
                          <?php
                        }
                      }
                    }
                    ?>
                </select>
              </div>
            </div>
            <div class="cell right">
              <h4 class="cell_title">Term</h4>
              <div class="cell_content editable">
                <select class="edit borderless" data-table=".exams" id="term">
                  <?php
                    $terms = $utk->FetchTerms();
                    foreach ($terms as $key => $value) {
                      if ($key == 0) {
                        ?>
                        <?php
                      }else {
                        if ($exams['term'] == $key) {
                          ?>
                          <option selected value="<?php echo $key ?>"><?php echo $value?></option>
                          <?php
                        }else {
                          ?>
                          <option value="<?php echo $key ?>"><?php echo $value?></option>
                          <?php
                        }
                      }
                    }
                    ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="cell left">
              <h4 class="cell_title">Exam Type</h4>
              <div class="cell_content editable">
                <select class="edit borderless" data-table=".exams" id="type">
                  <?php
                    $exam_types = $utk->FetchExamTypes();
                    foreach ($exam_types as $key => $value) {
                      if ($key == 0) {
                        ?>
                        <?php
                      }else {
                        if ($exams['type'] == $key) {
                          ?>
                          <option selected value="<?php echo $key ?>"><?php echo $value?></option>
                          <?php
                        }else {
                          ?>
                          <option value="<?php echo $key ?>"><?php echo $value?></option>
                          <?php
                        }
                      }
                    }
                    ?>
                </select>
              </div>
            </div>
            <div class="cell right">
              <h4 class="cell_title">Exam Due</h4>
              <div class="cell_content editable">
                <input type="date" class="edit borderless" data-table=".exams" id="due" value="<?php echo $exams['due'] ?>"></input>
              </div>
            </div>
          </div>
          <h1 id="update_exam" class="button_link">Update Exam <a></a> </h1><br><br>
        </div>
      </div>
      <br><br><br>
      <div class="">
        <div class="expandable" id="questions">
          <h1 class="expandable_title">Questions<span class="toggle toggle_close"></span></h1>
          <div class="expandable_content">
            <?php
              foreach ($qanda as $key => $value) {
                $contents = $value['content'];
                ?>
                <div class="a_question expandable" id="<?php echo $contents['id'] ?>">
                  <h1 class="expandable_title">Question No.<input type="number" class="question_number borderless small_width" value="<?php echo $contents['question_number']; ?>"> </span><span class="close close_big remove_question"></span> <span class="toggle toggle_open"></span></h1><br><br>
                  <div class="expandable_content hide qanda" id="<?php echo "qanda_".$contents['question_number'] ?>">
                    <div class="row">
                      <div class="cell left">
                        <h4 class="cell_title">Question</h4>
                        <div class="cell_content editable">
                          <textarea class="question borderless edit" name="question" rows="8" cols="80"><?php echo $contents['question']; ?></textarea>
                        </div>
                      </div>
                      <div class="cell right">
                        <h4 class="cell_title">Correct Anser</h4>
                        <div class="cell_content editable">
                          <select  type="text"  class="borderless edit correct">
                            <?php
                              foreach ($correct_answers as $key => $value) {
                                if ($value == $contents['correct']) {
                                  ?>
                                  <option value="<?php echo $value ?>" selected><?php echo $value ?></option>
                                  <?php
                                }else {
                                  ?>
                                  <option value="<?php echo $value ?>"><?php echo $value ?></option>
                                  <?php
                                }
                              }
                             ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="answers">
                      <div class="row">
                        <div class="cell left">
                          <h4 class="cell_title">A</h4>
                          <div class="cell_content editable">
                            <input  type="text" class="borderless edit a" name="a" value="<?php echo $contents['a']; ?>">
                          </div>
                        </div>
                        <div class="cell right">
                          <h4 class="cell_title">B</h4>
                          <div class="cell_content editable">
                            <input  type="text" class="borderless edit b" name="b" value="<?php echo $contents['b']; ?>">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="cell left">
                          <h4 class="cell_title">C</h4>
                          <div class="cell_content editable">
                            <input  type="text" class="borderless edit c" name="c" value="<?php echo $contents['c']; ?>">
                          </div>
                        </div>
                        <div class="cell right">
                          <h4 class="cell_title">D</h4>
                          <div class="cell_content editable">
                            <input  type="text" class="borderless edit d" name="d" value="<?php echo $contents['d']; ?>"><br>
                          </div>
                        </div>
                      </div>
                      <h1 class="button_link update_qanda">Update Q&A<a></a> </h1><br><br>
                  </div>
                </div>
              </div>
                <?php
              }
             ?>
          <form class="form expandable" id="add_new_question" action="" method="post">
            <h1 class="expandable_title">New Question No.<input type="number" id="question_number" value="" class="small_width"> </span></span> <span class="toggle toggle_open"></span></h1><br><br>
            <div class="expandable_content hide">
              <div>
                <label for="question">Question</label>
                <textarea id="question" name="question" rows="1" cols="80"></textarea>
              </div>
              <div class="answers">
                <label for="a">A</label>
                <input type="text" name="a" id="a" placeholder="A"><br>
                <label for="b">B</label>
                <input type="text" name="b" id="b" placeholder="B"><br>
                <label for="c">C</label>
                <input type="text" name="c" id="c" placeholder="C"><br>
                <label for="d">D</label>
                <input type="text" name="d" id="d" placeholder="D"><br>
              </div>
              <div>
                <label for="correct">Correct Answer</label>
                <select name="correct" id="correct">
                  <option selected disabled>Select Correct Answer</option>
                  <?php
                    foreach ($correct_answers as $key => $value) {
                      ?>
                      <option value="<?php echo $value ?>"><?php echo $value ?></option>
                      <?php
                    }
                   ?>
                </select>
              </div>
              <div class="submit">
                <button id="submit">Add New Question</button>
              </div>
            </div>
          </form>
          <br><br>
        </div>
        <br><br>
      </div>
      <br><br>

      <?php
    }
 ?>
