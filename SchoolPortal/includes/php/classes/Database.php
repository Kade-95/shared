<?php
  namespace classes;
  /**
   *
   */
  class Database{

    public $con;
    public $db;

    function __construct($db){
      global $root;
      $this->db = $db;
      $this->con = $this->Connect();
      $this->error_creating = "Error creating ";
    }

    //functions for connection and creation of tables
    private function Connect(){
      $this->con = mysqli_connect("localhost", "root", "");
      if(mysqli_connect_errno()){
        echo "Error connect to database " + mysqli_connect_errno() ;
        return false;
      }else {
        $this->SetupDB($this->db);
        return $this->con;
      }
    }

    private function SetupDB($db) {
      $this->CreateDB($db);
      $this->CreateMessageTable();
      $this->CreateStaffTable();
      $this->CreateStudentTable();
      $this->CreateNotificationTable();
      $this->CreateGenderTable();
      $this->CreateMaritalStatusTable();
      $this->CreateBloodGroupTable();
      $this->CreateRatingTable();
      $this->CreateCountryTable();
      $this->CreateStateTable();
      $this->CreateCityTable();
      $this->CreateAdminTable();
      $this->CreatePersistentTable();
      $this->CreateSessionsTable();
      $this->CreateClassesTable();
      $this->CreateClassSectionsTable();
      $this->CreateSubjectsTable();
      $this->CreateAllocatedSubjectsTable();
      $this->CreateExamsTable();
      $this->CreateQandATable();
      $this->CreateExamSessionsTable();
      $this->CreateScoresTable();
      $this->CreateResultsTable();
    }

    private function CreateDB($db){
      $sql = "create database if not exists ".$db;
      if(!mysqli_query($this->con, $sql)){
        echo $this->error_creating.$db;
      }
    }

    private function CreateSubjectsTable(){
      $table = $this->db.'.subjects';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, title varchar(200))";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateScoresTable(){
      $table = $this->db.'.scores';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, owner char(8), exam int(100), score varchar(32), qanda LONGTEXT)";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateResultsTable(){
      $table = $this->db.'.results';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, academic_session varchar(32), term varchar(32), class int(32), owner char(8), subject int(32), first_assignment varchar(32), second_assignment varchar(32), third_assignment varchar(32), quiz varchar(32), exam varchar(32), total varchar(32))";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateAllocatedSubjectsTable(){
      $table = $this->db.'.allocated_subjects';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, subject int(100), teacher char(8), class_section int(100))";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateExamsTable(){
      $table = $this->db.'.exams';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, type varchar(32), subject int(100), class int(100), academic_session varchar(32), term varchar(32), due date)";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateQandATable(){
      $table = $this->db.'.qanda';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, owner int(100), question LONGTEXT, question_number int(32), a LONGTEXT, b LONGTEXT, c LONGTEXT, d LONGTEXT, correct char(1))";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateExamSessionsTable(){
      $table = $this->db.'.exam_sessions';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, owner char(8), exam int(32), qanda varchar(1000), remaining_time int(100))";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreatePersistentTable(){
      $table = $this->db.'.persistent';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (p_key int(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, token varchar(32), s_ukey varchar(32), expiry varchar(225), created date, used boolean, browser text)";
        if (mysqli_query($this->con, $sql)) {
          return true;
        }
      }
    }

    private function CreateSessionsTable(){
      $table = $this->db.'.sessions';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id varchar(32) NOT NULL PRIMARY KEY, access int(10), data text(3000) DEFAULT NULL, ip varchar(100), email varchar(100), url varchar(100), browser text)";
        if(mysqli_query($this->con, $sql)){
          return true;
        }
      }
    }

    private function CreateClassesTable() {
      $table = $this->db.'.classes';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int PRIMARY KEY  NOT NULL AUTO_INCREMENT, title varchar(32))";
        if(mysqli_query($this->con, $sql)){
          return true;
        }
      }
    }

    private function CreateClassSectionsTable() {
      $table = $this->db.'.class_sections';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (id int PRIMARY KEY NOT NULL AUTO_INCREMENT, section varchar(32), class varchar(32), form_teacher char(8), student_no int(100), subject_no int(100))";
        if(mysqli_query($this->con, $sql)){
          return true;
        }
      }
    }

    private function CreateMessageTable(){
      $table = $this->db.'.messages';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (mId int PRIMARY KEY NOT NULL AUTO_INCREMENT, mFrom varchar(32), mTo text, mContent text, mImage varchar(32), mUser varchar(32), mRead int, mSeen int, mSent int, mTime varchar(32), mDate varchar(32))";
        if(!mysqli_query($this->con, $sql)){
          echo $this->error_creating.$table;
        }
      }
    }

    private function CreateAdminTable(){
      $table = $this->db.'.admin';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (e_key char(8) PRIMARY KEY, first_name varchar(225), sur_name varchar(225), middle_name varchar(225), user_name varchar(225) UNIQUE, nationality varchar(32), address varchar(225), city varchar(225), state varchar(225), mobile_phone varchar(20), email varchar(225) UNIQUE, password varchar(225), image varchar(225), status varchar(32), hire_date date, birth_date date, gender varchar(11), marital_status varchar(11), full_name varchar(225), autologin boolean, confirmed boolean)";
        if (!mysqli_query($this->con, $sql)) {
          echo $this->error_creating.$table;
        }
      }
    }

    private function CreateStaffTable(){
      $table = $this->db.'.staff';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (e_key char(8) PRIMARY KEY, first_name varchar(225), sur_name varchar(225), middle_name varchar(225), user_name varchar(225) UNIQUE, nationality varchar(32), address varchar(225), city varchar(225), state varchar(225), mobile_phone varchar(20), email varchar(225) UNIQUE, password varchar(225), image varchar(225), status varchar(32), charge int(2), open_date date, birth_date date, gender varchar(11), marital_status varchar(11), full_name varchar(225), autologin boolean, confirmed boolean, com_code varchar(32), retired int(2), retire_date date)";
        if (!mysqli_query($this->con, $sql)) {
          echo $this->error_creating.$table;
        }
      }
    }

    private function CreateStudentTable(){
      $table = $this->db.'.student';
      if (!$this->IsTable($table)) {
        $sql = "CREATE TABLE $table (e_key char(8) PRIMARY KEY, first_name varchar(225), sur_name varchar(225), middle_name varchar(225), user_name varchar(225) UNIQUE, nationality varchar(32), address varchar(225), city varchar(225), state varchar(225), mobile_phone varchar(20), email varchar(225) UNIQUE, password varchar(225), image varchar(225), class_sections varchar(32), class varchar(32), status varchar(32), open_date date, birth_date date, gender varchar(11), marital_status varchar(11), full_name varchar(225), autologin boolean, confirmed boolean, com_code varchar(32))";
        if (!mysqli_query($this->con, $sql)) {
          echo $this->error_creating.$table;
        }
      }
    }

    private function CreateNotificationTable(){
      $table = $this->db.'.notification';
      if(!$this->IsTable($table)){
        $sql = "CREATE TABLE $table (note_key int PRIMARY KEY NOT NULL AUTO_INCREMENT, user varchar(32), subject text, message text, notification_date date, status int)";
        if (!mysqli_query($this->con, $sql)) {
          echo $this->error_creating.$table;
        }
      }
    }

    private function CreateGenderTable(){
      $table = $this->db.'.gender';
      if(!$this->IsTable($table)){
        $sql = "CREATE TABLE $table(gender_key int PRIMARY KEY NOT NULL AUTO_INCREMENT, title varchar(32))";
        if(mysqli_query($this->con, $sql)){
          $sql = "INSERT INTO $table(title) VALUES('male'), ('female')";
          if (!$this->Insert($sql)) {
            echo $this->error_creating.$table;
          }
        }
      }
    }

    private function CreateMaritalStatusTable(){
      $table = $this->db.'.marital_status';
      if(!$this->IsTable($table)){
        $sql = "CREATE TABLE $table(ms_key int PRIMARY KEY NOT NULL AUTO_INCREMENT, title varchar(32))";
        if(mysqli_query($this->con, $sql)){
          $sql = "INSERT INTO $table(title) VALUES('single'), ('married')";
          if (!$this->Insert($sql)) {
            echo $this->error_creating.$table;
          }
        }
      }
    }

    private function CreateBloodGroupTable(){
      $table = $this->db.'.blood_group';
      if(!$this->IsTable($table)){
        $sql = "CREATE TABLE $table(bg_key int PRIMARY KEY NOT NULL AUTO_INCREMENT, title varchar(32))";
        if(mysqli_query($this->con, $sql)){
          $sql = "INSERT INTO $table(title) VALUES('O+'), ('O-'), ('AB'), ('A'), ('B')";
          if (!$this->Insert($sql)) {
            echo $this->error_creating.$table;
          }
        }
      }
    }

    private function CreateRatingTable(){
      $table = $this->db.'.rating';
      if(!$this->IsTable($table)){
        $sql = "CREATE TABLE $table(rating_key int PRIMARY KEY NOT NULL AUTO_INCREMENT, title varchar(32))";

        if(mysqli_query($this->con, $sql)){
          $sql = "INSERT INTO $table(title) VALUES('n/a'), ('poor'), ('fair'), ('very good'), ('excellent')";
          if (!$this->Insert($sql)) {
            echo $this->error_creating.$table;
          }
        }
      }
    }

    private function CreateCountryTable(){
      global $root;
      $table = $this->db.'.countries';
      if(!$this->IsTable($table)){
        $file = $root."includes/sql/countries.sql";
        $data = fopen($file, 'r') or die("Unable to open file");

        $sql = "CREATE TABLE $table (country_key int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT, sortname varchar(3) NOT NULL, name varchar(150) NOT NULL, phonecode int(11) NOT NULL)";

        if(mysqli_query($this->con, $sql)){
          $sql = fread($data, filesize($file));
          if(!mysqli_query($this->con, $sql)){
            echo $this->error_creating.$table;
          }
        }
      }
    }

    private function CreateStateTable(){
      global $root;
      $table = $this->db.'.states';
      if(!$this->IsTable($table)){
        $file = $root."includes/sql/states.sql";
        $data = fopen($file, 'r') or die("Unable to open file");

        $sql = "CREATE TABLE $table (state_key int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT, name varchar(30) NOT NULL, country_key int(11) NOT NULL)";

        if(mysqli_query($this->con, $sql)){
          $sql = fread($data, filesize($file));
          if(!mysqli_query($this->con, $sql)){
            echo $this->error_creating.$table;
          }
        }
      }
    }

    private function CreateCityTable(){
      global $root;
      $table = $this->db.'.cities';
      if(!$this->IsTable($table)){
        $file = $root."includes/sql/cities.sql";
        $data = fopen($file, 'r') or die("Unable to open file");

        $sql = "CREATE TABLE $table (city_key int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT, name varchar(30) NOT NULL, state_key int(11) NOT NULL)";

        if(mysqli_query($this->con, $sql)){
          $data = fread($data, filesize($file));
          $sql = explode('split', $data);
          $len = count($sql);
          for($i=0; $i<$len; $i++){
            if(!mysqli_query($this->con, $sql[$i])){
              echo $this->error_creating.$table;
            }
          }
        }
      }
    }

    public function Insert($sql){
      if(mysqli_query($this->con, $sql)){
        return true;
      }
      return false;
    }

    private function IsTable($table){
      $sql = "SELECT * FROM $table";
      if (mysqli_query($this->con, $sql)) {
        return true;
      }
      return false;
    }
  }
 ?>
