<?php
  namespace classes;

  /**
   *
   */
  class UTK{

    function __construct(){

    }

    public function FetchAcademicSession($years){
      $current = Date('Y');
      $sessions = ['Select Academic Session'];
      for ($i=0; $i < $years; $i++) {
        $first = $current+$i;
        $second = $first+1;
        $sessions[] = $first."/".$second;
      }
      return $sessions;
    }

    public function FetchTerms(){
      return ['Select Term', 'First Term', 'Second Term', 'Third Term'];
    }

    public function FetchExamTypes(){
      return ['Select Exam Types', 'First Assignment', 'Second Assignment', 'Third Assignment', 'Forth Assignment', 'Quiz', 'Exam'];
    }

    public function FetchExamPercentage(){
      return [0, 10/3, 10/3, 10/3, 20, 70];
    }

    public function FetAnswerOptions(){
      return ['A', 'B', 'C', 'D'];
    }

    function IsOnline(){
      $connected = @fopen("https://www.google.com/", 80);
      if ($connected){
        fclose($connected);
        return true;
      }
    }

    public function SetLogin($owner, $id){
      global $site, $query;
      $sql = "UPDATE $site.$owner SET status='1' WHERE e_key = '$id'";
      $query->Insert($sql);
    }

    public function SetLogout($owner, $id){
      global $site, $query, $utk;
      $sql = "UPDATE $site.$owner SET status='0' WHERE e_key = '$id'";
      $utk->Alert($sql);
      $query->Insert($sql);
    }


    public function IgnoreReferer(){
      global $man;
      $ignore_referer = ['index.php?sign_in', 'index.php?staff=retire', 'index.php?staff=activate', 'index.php?admin=remove', 'index.php?admin=make', 'index.php?subjects=de_allocate', 'index.php?class_sections=delete', 'index.php?subjects=delete', 'index.php?classes=delete', 'index.php?students=delete', 'index.php?staff=delete'];
      foreach ($ignore_referer as $key => $value) {
        if ($man->HasString($this->GetURI(), $value)) {
          return true;
        }
      }
      return false;
    }

    function GetURI(){
      return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    function Alert($value){
      echo '<script>alert("'.$value.'")</script>';
    }

    function Redirect($value){
      echo "<script>window.open('$value', '_self')</script>";
    }

    function AddCommaToMoney($value){
      $inverse = '';

      for ($i=strlen($value)-1; $i >= 0 ; $i--) {
        $inverse .= $value[$i];
      }

      $value = '';

      for ($i=0; $i < strlen($inverse); $i++) {
        $position = ($i+1)%3;
        $value .= $inverse[$i];
        if ($i == strlen($inverse) - 1) {
          break;
        }
        if ($position == 0) {
          $value .= ',';
        }
      }

      $inverse = '';
      for ($i=strlen($value)-1; $i >= 0 ; $i--) {
        $inverse .= $value[$i];
      }
      return $inverse;
    }


    function GetFolderContents($folder){
      $desc_contents = [];
      $desc = '';
      $size = '';
      $date = '';
      $access = '';
      $contents = array_diff(scandir($folder, 1), array("..", "."));// Get all the contents of a folder
      foreach ($contents as $key => $name) {
        if (is_dir($folder."/".$name)){
          $desc = 1;
        }elseif (is_file($folder."/".$name)){
          $desc = 0;
        }
        $size = floor(filesize($folder."/".$name)/1024);
        $date = date('d/m/Y', filemtime($folder."/".$name));
        $access = 'none';
        $desc_contents[$key] = ['name'=>$name, 'desc'=>$desc, 'size'=>$size, 'date'=>$date, 'access'=>$access];
      }
      $desc_contents = $this->MultiDimensionSorter($desc_contents, "desc");// Sort folder contents alphabetically starting with folders
      return $desc_contents;
    }

    function GetIp(){
      $ip = '';
      if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
      return $ip;
    }

    function MultiDimensionSorter($array, $tag){
      $num = count($array);
      $flag = true;
      $temp = [];
      while ($flag) {
        $flag = false;
        for ($i=0; $i < $num-1; $i++){
          if ($array[$i][$tag] < $array[$i+1][$tag]) {
            $flag = true;
            $temp = $array[$i];
            $array[$i] = $array[$i+1];
            $array[$i+1] = $temp;
          }
        }
      }
      return $array;
    }

    function IsImageFile($value){
      $ext = strrchr($value, '.');
      if($ext == ".jpeg" || $ext == ".gif" || $ext == ".jpg" || $ext == ".png"){
        return true;
      }
      return false;
    }

    function DeleteFiles($target){
      if(is_dir($target)){
        $files = glob($target.'*', GLOB_MARK);
        foreach ($files as $file) {
          $this->DeleteFiles($file);
        }
        if(file_exists($target)){
          rmdir($target);
        }
      }elseif (is_file($target)) {
        unlink($target);
      }
    }

    function CopyFiles($source, $destination){
      if (is_dir($source)) {
        //re-create the directory if folder does not exist
         if(!is_dir($destination)){
           mkdir($destination);
         }
        // //get the contents of the original directory
        foreach ($this->GetFolderContents($source) as $key => $value) {
          //copy them to the new directory
          $this->CopyFiles($source.'/'.$value['name'], $destination.'/'.$value['name']);
        }
      }
      else {
        if (!copy($source, $destination)) {
          return 0;
        }
      }
      return 1;
    }

    function IsAdminPage(){
      if(isset($_SESSION['admin_session'])){
        return true;
      }
      return false;
    }
  }
 ?>
