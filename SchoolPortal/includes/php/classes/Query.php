<?php
  namespace classes;
  /**
   *
   */
  class Query{
    public $con;
    function __construct($con){
      $this->con = $con;
      global $root;
      if (!file_exists($root."staff")) {
        mkdir($root."staff");
      }
      if (!file_exists($root."retired")) {
        mkdir($root."retired");
      }
      if (!file_exists($root."tmp")) {
        mkdir($root."tmp");
      }
    }

    public function Insert($sql){
      if(mysqli_query($this->con, $sql)){
        return true;
      }
      return false;
    }

    public function GetColumnFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $col_return){
      $rows = $this->GetRowsFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $col_return);
      $column = [];
      foreach ($rows as $key => $value) {
        $column[] = $value['id'];
      }
      return $column;
    }

    public function GetFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $col_return){
      $sql = "";
      if($col_name1 == "" && $col_name2 == ""){
        $sql = "SELECT * FROM $table";
      }elseif ($col_name1 == ""){
        $sql = "SELECT * FROM $table WHERE $col_name2='$col_value2'";
      }elseif ($col_name2 == ""){
        $sql = "SELECT * FROM $table WHERE $col_name1='$col_value1'";
      }elseif($col_name1 != "" && $col_name2 != ""){
        $sql = "SELECT * FROM $table WHERE $col_name1='$col_value1' AND $col_name2='$col_value2'";
      }
      $run = mysqli_query($this->con, $sql);
      $row = mysqli_fetch_array($run);
      if($col_return == ""){
        return $row;
      }else {
        return (string)$row[$col_return];
      }
    }

    public function GetRowsFromTable($table, $col_name1, $col_value1, $col_name2, $col_value2, $id){
      $rows = [];
      $sql = "";
      if($col_name1 == "" && $col_name2 == ""){
        $sql = "SELECT * FROM $table";
      }elseif ($col_name1 == ""){
        $sql = "SELECT * FROM $table WHERE $col_name2='$col_value2'";
      }elseif ($col_name2 == ""){
        $sql = "SELECT * FROM $table WHERE $col_name1='$col_value1'";
      }elseif($col_name1 != "" && $col_name2 != ""){
        $sql = "SELECT * FROM $table WHERE $col_name1='$col_value1' AND $col_name2='$col_value2'";
      }
      $run = mysqli_query($this->con, $sql);
      $i = 0;
      while ($row = mysqli_fetch_array($run)) {
        $rows[$i] = ['id'=>$row[$id], 'content'=>$row];
        $i++;
      }
      return $rows;
    }

    public function DeleteFromDB($table, $column, $group){
      $sql = "DELETE FROM $table WHERE $column='$group'";
      if(mysqli_query($this->con, $sql)){
        return true;
      }
      return false;
    }

    public function DoesRowsExist($table, $col_names, $col_values){
      if (count($col_names) != count($col_values)) {
        echo "not equal arrays ".count($col_names).':'.count($col_values);
        return false;
      }
      $sql = "SELECT * FROM $table WHERE ";
      foreach ($col_values as $key => $value) {
        $sql .= "$col_names[$key]='$col_values[$key]'";
        if($key+1 != count($col_values)){
          $sql .= 'AND ';
        }else{
          $sql .= ' ';
        }
      }
      return mysqli_num_rows(mysqli_query($this->con, $sql));
    }

    public function GetLastRowId($table, $id){
      $sql = "SELECT * FROM $table ORDER BY $id DESC LIMIT 1";
      $run = mysqli_query($this->con, $sql);
      $row = mysqli_fetch_array($run);
      return (String)$row[$id];
    }

    public function GenerateUserKey($user_name, $owner){
      global $user, $site;
      $e_key = hash('crc32', microtime(true) . mt_rand() . $user_name);
      $keys = $this->GetColumnFromTable($site.'.'.$owner, '', '', '', '', 'e_key');
      foreach ($keys as $key => $value) {
        if ($value == $e_key) {
          $e_key = $this->GenerateUserKey($user_name, $owner);
        }
      }
      return $e_key;
    }

    public function SendNotification($user, $subject, $message){
      $sql = "INSERT INTO SIMS.notification (user, subject, message, notification_date, status) VALUES('$user', '$subject', '$message', now(), 0)";
      return mysqli_query($this->con, $sql);
    }

    public function MarkExam($exam, $qandas){
      global $site, $utk;
      $marking_scheme = $this->GetRowsFromTable($site.'.qanda', 'owner', $exam, '', '', 'id');
      $total = count($marking_scheme);
      $correct = 0;
      foreach ($marking_scheme as $key => $value) {
        $marking_scheme_contents = $value['content'];
        foreach ($qandas as $num => $qanda) {
          if ($marking_scheme_contents['question_number'] == $qanda->question_number) {
            if ($marking_scheme_contents['correct'] == $qanda->answer) {
              $correct++;
            }
          }
        }
      }
      $exam_type = $this->GetFromTable($site.'.exams', 'id', $exam, '', '', 'type');
      return ($correct/$total) * $utk->FetchExamPercentage()[$exam_type];
    }

    public function GetResults($academic_session, $term){
      global $site, $man;
      $exams = $this->GetRowsFromTable($site.'.exams', 'academic_session', $academic_session, 'term', $term, 'id');
      $results = [];
      foreach ($exams as $key => $value) {
        //For all exams written in this term
        $exam_contents = $value['content'];
        $classes = $this->GetColumnFromTable($site.'.classes', '', '', '', '', 'id');
        $students = $this->GetColumnFromTable($site.'.student', '', '', '', '', 'e_key');
        $subjects = $this->GetColumnFromTable($site.'.subjects', '', '', '', '', 'id');

        $scores = $this->GetRowsFromTable($site.'.scores', 'exam', $exam_contents['id'], '', '', 'id');
        //get all the scores in the term
        foreach ($scores as $single => $score) {
          $score_contents = $score['content'];
          $results[] = ['owner' => $exam_contents['class'].$score_contents['owner'].$exam_contents['subject'], 'class' => $exam_contents['class'], 'student' => $score_contents['owner'], 'subject' => $exam_contents['subject'], 'type' => $exam_contents['type'], 'score' => round($score_contents['score'], 2)];
        }
      }

      $owners = [];
      foreach ($results as $key => $value) {
        //Generate a key for a particular exam belonging to a student
        if (!$man->HasString($owners, $value['owner'])) {
          $owners[] = $value['owner'];
        }
      }
      $scores = [];

      foreach ($owners as $key => $value) {
        //for each key
        $score = 0;
        $types = [];
        foreach ($results as $r => $result) {
          if ($result['owner'] == $value) {
            //add all the exams
            $score += $result['score'];
            $types[] = ['type' => $result['type'], 'score' => $result['score']];
          }
        }
        $scores[] = ['class' => $result['class'], 'student' => $result['student'], 'subject' => $result['subject'], 'class' => $result['class'] , 'score' => $score, 'type' => $types];
      }

      return $scores;
    }
  }
 ?>
