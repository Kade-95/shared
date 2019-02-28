<?php

namespace classes\Sessions;

class MysqliSessionsHandler{

    function __construct(){
      global $utk, $site;
      session_set_save_handler(
        array($this, "_open"),
        array($this, "_close"),
        array($this, "_read"),
        array($this, "_write"),
        array($this, "_destroy"),
        array($this, "_gc")
      );

      $this->table = $site.'.sessions';
      session_start();
    }

    public function _open(){
      global $query;

      // If successful
      if($query->con){
        // Return True
        return true;
      }
      // Return False
      return false;
    }

    public function _close(){
      // Close the database connection
      // If successful
      global $query;
      if(mysqli_close($query->con)){
        // Return True
        return true;
      }
      // Return False
      return false;
    }

    function _read($id){
      global $query;

      $sql = "SELECT * FROM $this->table WHERE  id = '$id'";

      if ($result = mysqli_query($query->con, $sql)) {
          $record = mysqli_fetch_array($result);
          return (string)$record['data'];
      }

      return '';
    }

    public function _write($id, $data){
      global $user, $utk, $query;
      // Create time stamp
      $access = time();

      $email = '';
      $browser = $_SERVER['HTTP_USER_AGENT'];
      if (isset($_SESSION['session'])) {
        $email = $_SESSION['session'];
      }
      $url = "";

      // Set query
      $ip = $utk->GetIp();

      if($email != ""){
        $sql = "SELECT * FROM $this->table WHERE email='$email' AND ip='$ip' AND browser='$browser'";
        $run = mysqli_query($query->con, $sql);
        $no_rows = mysqli_num_rows($run);
        if ($no_rows) {
          $sql = "DELETE FROM $this->table WHERE email='$email' AND ip='$ip' AND browser='$browser'";
          mysqli_query($query->con, $sql);
        }
      }else {
        $sql = "DELETE FROM $this->table WHERE email='$email' AND ip='$ip' AND browser='$browser'";
        mysqli_query($query->con, $sql);
      }
      
      $sql = "REPLACE INTO $this->table (id, access, data, ip, email, url, browser) VALUES ('$id', '$access', '$data' ,'$ip', '$email', '$url', '$browser')";
      return mysqli_query($query->con, $sql);
    }

    public function _destroy($id){
      // Set query
      global $query;

      $sql = "DELETE FROM $this->table WHERE id = '$id'";

      if(mysqli_query($query->con, $sql)){
        return True;
      }
      return false;
    }

    public function _gc($max){
      global $query;

      // Calculate what is to be deemed old
      $old = time() - $max;

      // Set query
      $old = mysql_real_escape_string($old);

      $sql = "DELETE FROM $this->table WHERE access < '$old'";

      return mysql_query($query->con, $sql);

      // Return False
      return false;
    }

    private function GetAccess($id){
      global $query;

      $sql = "select * from $this->table where email='$id'";
      if($record = mysqli_query($query->con, $sql)){
        $result = mysqli_fetch_array($record);
        return $result['access'];
      }
      return false;
    }

    public function IsUserExpired($id){
      $idleTime = 6000;
      if(time() - (int)$this->GetAccess($id) > $idleTime){
        return true;
      }
      return false;
    }
  }
 ?>
