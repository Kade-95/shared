<?php
  namespace classes;
  class StringManipulator{

    function __construct(){
    }

    private $capitals = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private $smalls = "abcdefghijklmnopqrstuvwxyz";
    private $digits = "1234567890";
    private $symbols = ",./?'!@#$%^&*()-_+=`~\\| ";

    function GetPostionOfAlpha($value, $size){
      if($size == "smalls"){
        for($i=0; $i<strlen($this->smalls); $i++){
          if($value == $this->smalls[$i]){
            return $i;
          }
        }
      }elseif($size == "capitals"){
        for($i=0; $i<strlen($this->capitals); $i++){
          if($value == $this->capitals[$i]){
            return $i;
          }
        }
      }
    }

    function ChangeCase($value, $size){
      $pos = 0;
      if ($size == "smalls") {
        if($this->IsCapital($value)) {
          $pos = $this->GetPostionOfAlpha($value, 'capitals');
          return $this->smalls[$pos];;
        }
      }elseif ($size == "capitals") {
        if ($this->IsSmall($value)) {
          $pos = $this->GetPostionOfAlpha($value, 'smalls');
          return $this->capitals[$pos];
        }
      }
      return $value;
    }

    public function ToLowerCase($value){
      $return = "";
      for ($i=0; $i < strlen($value); $i++) {
        $return .= $this->ChangeCase($value[$i], "smalls");
      }
      return $return;
    }

    public function ToUpperCase($value){
      $return = "";
      for ($i=0; $i < strlen($value); $i++) {
        $return .= $this->ChangeCase($value[$i], "capitals");
      }
      return $return;
    }

    public function ToSentenceCase($value){
      $value[0] = $this->ToUpperCase($value[0]);
      return $value;
    }

    public function IsCapital($value){
      for($i=0; $i<strlen($this->capitals); $i++){
        if($value == $this->capitals[$i]){
          return true;
        }
      }
      return false;
    }

    public function IsSmall($value){
      for($i=0; $i<strlen($this->smalls); $i++){
        if($value == $this->smalls[$i]){
          return true;
        }
      }
      return false;
    }

    public function IsDigit($value){
      for($i=0; $i<strlen($this->digits); $i++){
        if($value == $this->digits[$i]){
          return true;
        }
      }
      return false;
    }

    public function IsNumber($value){
      for($j=0; $j<strlen($value); $j++){
        if(!$this->IsDigit($value[$j])){
          return false;
        }
      }
      return true;
    }

    public function IsSymbol($value){
      for($i=0; $i<strlen($this->symbols); $i++){
        if($value == $this->symbols[$i]){
          return true;
        }
      }
      return false;
    }

    public function IsEmail($value){
      if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
        return false;
      }
      return true;
    }

    public function IsSpace($value){
      if($value == " "){
        return true;
      }
      return false;
    }

    public function IsSpaceString($value){
      for($i=0; $i<strlen($value); $i++){
        if(!$this->IsSpace($value[$i])){
          return false;
        }
      }
      return true;
    }

    public function IsPasswordValid($value){
      $len = strlen($value);
      if($len >= 8){
        for($j=0; $j<strlen($value); $j++){
          if($this->IsCapital($value[$j])){
            for($k=0; $k<strlen($value); $k++){
              if($this->IsSmall($value[$k])){
                for($l=0; $l<strlen($value); $l++){
                  if($this->IsDigit($value[$l])){
                    for($m=0; $m<strlen($value); $m++){
                      if($this->IsSymbol($value[$m])){
                        return true;
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      return false;
    }

    public function IsDateValid($value){
      if($this->IsDate($value)){
        if($this->IsYearValid($value)){
          if($this->IsMonthValid($value)){
            if($this->IsDayValid($value)){
              return true;
            }
          }
        }
      }
      return false;
    }

    public function IsDate($value){
      $len = strlen($value);
      if($len == 10){
        for($i=0; $i<$len; $i++){
          if($this->IsDigit($value[$i])){
            continue;
          }else{
            if($i == 4 || $i == 7){
              if($value[$i] == "-"){
                continue;
              }else {
                return false;
              }
            }else {
              return false;
            }
          }
        }
      }else {
        return false;
      }
      return true;
    }

    public function IsOldEnough($value){
      $year = date('Y');
      $check = $year - $value;
      if($check >= 18){
        return true;
      }
      return false;
    }

    public function IsYearValid($value){
      $year = date('Y');
      $v_year = "";
      for($i=0; $i<4; $i++){
        $v_year .= $value[$i+0];
      }
      if($v_year>$year){
        return 0;
      }
      return $v_year;
    }

    public function IsLeapYear($year){
      if($year%4 == 0){
        if(($year%100 == 0) & ($year%400 != 0)){
          return false;
        }
        return true;
      }
      return false;
    }

    public function IsMonthValid($value){
      $v_month = "";
      for($i=0; $i<2; $i++){
        $v_month .= $value[$i+5];
      }
      if($v_month > 12 || $v_month < 1){
        return 0;
      }
      return $v_month;
    }

    public function IsDayValid($value){
      $v_day = "";
      for($i=0; $i<2; $i++){
        $v_day .= $value[$i+8];
      }
      $limit = 0;
      $month = $this->IsMonthValid($value);

      if($month == '01'){
        $limit = 31;
      }elseif ($month == '02') {
        if($this->IsLeapYear($this->IsYearValid($value))){
          $limit = 29;
        }else {
          $limit = 28;
        }
      }elseif ($month == '03') {
        $limit = 31;
      }elseif ($month == '04') {
        $limit = 30;
      }elseif ($month == '05') {
        $limit = 31;
      }elseif ($month == '06') {
        $limit = 30;
      }elseif ($month == '07') {
        $limit = 31;
      }elseif ($month == '08') {
        $limit = 31;
      }elseif ($month == '09') {
        $limit = 30;
      }elseif ($month == '10') {
        $limit = 31;
      }elseif ($month == '11') {
        $limit = 30;
      }elseif ($month == '12') {
        $limit = 31;
      }

      if($limit<$v_day){
        return 0;
      }
      return $v_day;
    }

    public function IsStringEmpty($value=''){
      if($value == ""){
        return "Not Set";
      }
      return $value;
    }

    public function StringReplace($value, $from, $to){
      $value = explode($from, $value);
      return implode($to, $value);
    }

    public function IsSubString($haystack, $needle){
      $tmp = explode($needle, $haystack);
      $tmp = implode('', $tmp);
      if ($tmp == $haystack) {
        return false;
      }
      return true;
    }

    public function HasString($haystack, $needle){
      for ($i = 0; $i < count($haystack); $i++) {
        if ($haystack[$i] == $needle) {
          return true;
        }
      }
      return false;
    }

    public function InsertString($haystack, $needle, $position){
      $merge = str_split($haystack);
      $haystack = '';
      for ($i=0; $i < count($merge); $i++) {
        if ($i == $position) {
          $haystack .= $needle;
        }
        $haystack .= $merge[$i];
      }
      return $haystack;
    }

    public function GetStringAtPosition($haystack, $position){
      $merge = str_split($haystack);
      for ($i=0; $i < count($merge); $i++) {
        if ($i == $position) {
          return $merge[$i];
        }
      }
    }

    public function StringReplaceAtPosition($value, $to, $position){
      $value = str_split($value);
      for ($i=0; $i < count($value); $i++) {
        if ($i == $position) {
          $value[$i] = $to;
        }
      }
      return implode('', $value);
    }
  }
 ?>
