<?php
namespace classes\Sessions;
  class PersistentSessionsHandler extends MysqliSessionsHandler{

    private $table;
    private $expiry;
    private $cookie = "SchoolPortal";

    function __construct() {
      global $site;
      $this->table = $site.".Persistent";
      $this->expiry = time() + 60*60*24;
    }

    public function CheckCredentials(){
      if ($cookie = $this->GetCookie()) {
        if ($storedToken = $this->ParseCookie($cookie)) {
          //delete all the old
          if ($this->ClearOld()) {
             if ($this->CookieExists($cookie)) {
               if (!$this->HasTokenBeenUsed($storedToken)) {
                 if ($this->UseCookie($storedToken)) {
                   $_SESSION['user_session'] = $this->GetExistingData($cookie);
                   $newToken = $this->GeneraterToken();
                   $this->StoreToken($newToken);
                   $this->SetCookie($newToken);
                   return true;
                 }
               }else {
                 //suspect a hack
                 $this->DeleteAll();
                 session_destroy();
                 setcookie($this->cookie, '', time() + 0, '/', '', null, true);
                 return false;
              }
            }else {
              $this->DeleteAll();
              session_destroy();
              setcookie($this->cookie, '', time() + 0, '/', '', null, true);
              return false;
            }
          }
        }
      }
    }

    public function Logout($all){
      global $query, $site;
      if ($all) {
        $this->DeleteAll();
      }else {
        $token = $this->ParseCookie($this->GetCookie());
        $sql = "UPDATE $this->table SET used='1' WHERE token='$token'";
        if (mysqli_query($query->con, $sql)) {
          setcookie($this->cookie, '', time() + 0, '/', '', null, true);
          $e_key = $query->GetFromTable($site.'.driver', 'email', $_SESSION['user_session'], '', '', 'e_key');
          $sql = "UPDATE SIMS.driver SET autologin=NULL WHERE e_key='$e_key'";
          $query->Insert($sql);
        }
      }
      session_destroy();
    }

    public function MakePersistent(){
      $token = $this->GeneraterToken();
      $this->SetCookie($token);
      if ($this->StoreToken($token)) {
        return true;
      }
    }

    private function CookieExists($cookie){
      global $query, $site;
      $user_session = explode('|', $cookie)[0];
      $s_ukey = $query->GetFromTable($site.'.driver', 'email', $user_session, '', '', 'e_key');
      $token = $this->ParseCookie($cookie);
      return $query->DoesRowsExist($site.'.persistent', ['token', 's_ukey'], [$token, $s_ukey]);
    }

    private function SetCookie($token){
      global $query, $man, $site;
      $e_key = $query->GetFromTable($site.'.driver', 'email', $_SESSION['user_session'], '', '', 'e_key');
      $merged = str_split($token);
      $stored_token = [];
      foreach ($merged as $key => $value) {
        $stored_token[] = $value;
        if ($key%4 == 0) {
          $stored_token[] = $e_key[$key/4];
        }
      }
      $token = $_SESSION['user_session'].'|'.implode('', $stored_token);
      setcookie($this->cookie, $token, $this->expiry, '/', '', null, true);
     }

    private function StoreToken($token){
      global $query, $site;
      $browser = $_SERVER['HTTP_USER_AGENT'];
      $expiry = time()*30;
      $e_key = $query->GetFromTable($site.'.driver', 'email', $_SESSION['user_session'], '', '', 'e_key');

      $sql = "INSERT INTO $this->table (token, s_ukey, expiry, created, used, browser) VALUES('$token', '$e_key', '$this->expiry', now(), '0', '$browser')";
      if ($query->Insert($sql)) {
        $sql = "UPDATE SIMS.driver SET autologin=1 WHERE e_key='$e_key'";
        return $query->Insert($sql);
      }
    }

    private function GeneraterToken(){
      return bin2hex(openssl_random_pseudo_bytes(16));
    }

    private function GetCookie(){
      if (isset($_COOKIE[$this->cookie])) {
        return $_COOKIE[$this->cookie];
      }
      return false;
    }

    private function GetExistingData($cookie){
      return explode('|', $cookie)[0];
    }

    private function ParseCookie($cookie){
      global $man, $query, $site;
      $user_session = explode('|', $cookie)[0];
      $cookie = explode('|', $cookie)[1];
      $s_ukey = $query->GetFromTable($site.'.driver', 'email', $user_session, '', '', 'e_key');
      //check if the user id is valid
      $stored_token = str_split($cookie);
      $stored_key = [];
      $i = 1;
      foreach ($stored_token as $key => $value) {
        if ($key%4 == 0) {
          if (count($stored_key) < 8) {
            $stored_key[] = $stored_token[(int)$key + $i];
            $stored_token[(int)$key + $i] = '';
            $i++;
          }
        }
      }

      $stored_key = implode('', $stored_key);
      $stored_token = implode('', $stored_token);

      if ($stored_key == $s_ukey) {
        return $stored_token;
      }
      return false;
    }

    private function HasTokenBeenUsed($token){
      global $query, $site;
      $e_key = $query->GetFromTable($site.'.driver', 'email', $this->GetExistingData($this->GetCookie()), '', '', 'e_key');
      return $query->GetFromTable($this->table, 's_ukey', $e_key, 'token', $token, 'used');
    }

    private function UseCookie($token){
      global $query, $site;
      $e_key = $query->GetFromTable($site.'.driver', 'email', $this->GetExistingData($this->GetCookie()), '', '', 'e_key');
      $sql = "UPDATE $this->table SET used='1' WHERE token='$token' and s_ukey='$e_key'";
      if (mysqli_query($query->con, $sql)) {
        return true;
      }
      return false;
    }

    private function ClearOld(){
      global $query;
      $time = time();
      $sql = "DELETE FROM $this->table WHERE expiry < '$time'";
      if (mysqli_query($query->con, $sql)) {
        return true;
      }
      return false;
    }

    private function DeleteAll(){
      global $query, $site;
      $e_key = $query->GetFromTable($site.'.driver', 'email', $this->GetExistingData($this->GetCookie()), '', '', 'e_key');
      $sql = "DELETE FROM $this->table WHERE s_ukey = '$e_key'";
      return mysqli_query($query->con, $sql);
    }
  }

 ?>
