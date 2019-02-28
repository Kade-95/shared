class StringManipulator {

  constructor(url, user, date) {
    this.capitals = "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
    this.smalls = "abcdefghijklmnopqrstuvwxyz",
    this.digits = "1234567890",
    this.symbols = ",./?'!@#$%^&*()-_+=`~\\| ";
    this.date = date;
  }

  AddCommaToMoney(money) {
    var inverse = '';
    for (var i = money.length-1; i >= 0; i--) {
      inverse += money[i];
    }

    money = "";

    for (var i = 0; i < inverse.length; i++) {
      position = (i+1)%3;
      money += inverse[i];
      if (position == 0) {
        if(i != inverse.length-1){
          money += ',';
        }
      }
    }
    inverse = '';

    for (var i = money.length-1; i >= 0; i--) {
      inverse += money[i];
    }
    return inverse;
  }

  IsCapital(value){
    if (value.length == 1) {
      return this.IsSubString(this.capitals, value);
    }
  }

  IsSmall(value){
    if (value.length == 1) {
      return this.IsSubString(this.smalls, value);
    }
  }

  IsSymbol(value){
    if (value.length == 1) {
      return this.IsSubString(this.symbols, value);
    }
  }

  IsName(value) {
    for(var x in value){
      if (this.IsDigit(value[x])) {
        return false;
      }
    }
    return true;
  }

  IsNumber(value) {
    for(var x in value){
      if (!this.IsDigit(value[x])) {
        return false;
      }
    }
    return true;
  }

  IsPasswordValid(value){
    var len = value.length;
    if (len > 7) {
      for(var a in value){
        if (this.IsCapital(value[a])) {
          for(var b in value){
            if (this.IsSmall(value[b])) {
              for(var c in value){
                if (this.IsDigit(value[c])) {
                  for(var d in value){
                    if (this.IsSymbol(value[d])) {
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

  IsDigit(value){
    value = new String(value)
    if (value.length == 1) {
      return this.IsSubString(this.digits, value);
    }
    return false;
  }

  IsEmail(value) {
    var email_parts = value.split('@');
    if (email_parts.length != 2) {
      return false;
    }else {
      if (this.IsSpaceString(email_parts[0])) {
        return false;
      }
      var dot_parts = email_parts[1].split('.');
       if(dot_parts.length != 2){
         return false;
       }else {
         if (this.IsSpaceString(dot_parts[0])) {
           return false;
         }
         if (this.IsSpaceString(dot_parts[1])) {
           return false;
         }
       }
    }
    return true;
  }

  IsDateValid(value){
    if(this.IsDateFormat(value)){
      if(this.IsYearValid(value)){
        if(this.IsMonthValid(value)){
          if(this.IsDayValid(value)){
            return true;
          }
        }
      }
    }
    return false;
  }

  IsDayValid(value){
    var v_day = "";
    for(var i=0; i<2; i++){
      v_day += value[i+8];
    }
    var limit = 0;
    var month = this.IsMonthValid(value);

    if(month == '01'){
      limit = 31;
    }else if (month == '02') {
      if(this.IsLeapYear(this.IsYearValid(value))){
        limit = 29;
      }else {
        limit = 28;
      }
    }else if (month == '03') {
      limit = 31;
    }else if (month == '04') {
      limit = 30;
    }else if (month == '05') {
      limit = 31;
    }else if (month == '06') {
      limit = 30;
    }else if (month == '07') {
      limit = 31;
    }else if (month == '08') {
      limit = 31;
    }else if (month == '09') {
      limit = 30;
    }else if (month == '10') {
      limit = 31;
    }else if (month == '11') {
      limit = 30;
    }else if (month == '12') {
      limit = 31;
    }

    if(limit<v_day){
      return 0;
    }
    return v_day;
  }

  IsDateFormat(value){
    var len = value.length;
    if (len == 10) {
      for(var x in value){
        if (this.IsDigit(value[x])) {
          continue;
        }else {
          if (x == 4 || x == 7) {
            if (value[x] == '-') {
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

  IsMonthValid(value){
    var v_month = "";
    for(var i=0; i<2; i++){
      v_month += value[i+5];
    }
    if(v_month > 12 || v_month < 1){
      return 0;
    }
    return v_month;
  }

  IsYearValid(value){
    var year = this.date.getFullYear('Y');
    var v_year = "";
    for(var i=0; i<4; i++){
      v_year += value[i+0];
    }
    if(v_year>year){
      return 0;
    }
    return v_year;
  }

  IsLeapYear(value){
    if (value%4 == 0) {
      if ((value%100 == 0) && (value%400 != 0)) {
        return false;
      }
      return true;
    }
    return false;
  }

  IsSpaceString(value) {
    for(var x in value){
      if (value[x] != ' ') {
        return false;
      }
    }
    return true;
  }

  IsDate() {

  }

  DeleteFromArrayPosition(haystack, position) {
    var tmp = [];
    for (var i = 0; i < haystack.length; i++) {
      if (i == position) {
        continue;
      }
      tmp.push(haystack[i]);
    }
    return tmp;
  }

  InsertIntoArrayPosition(haystack, needle, insert) {
    var position = this.GetPositionOfArray(haystack, needle);
    var tmp = [];
    for (var i = 0; i < haystack.length; i++) {
      tmp.push(haystack[i]);
      if (i == position) {
        tmp.push(insert);
      }
    }
    return tmp;
  }

  GetPositionOfArray(haystack, needle) {
    for(var x in haystack){
      if (JSON.stringify(haystack[x]) == JSON.stringify(needle)) {
        return x;
      }
    }
    return false;
  }

  HasArray(haystack, needle) {
    haystack = JSON.stringify(haystack);
    needle = JSON.stringify(needle);

    return haystack.indexOf(needle);
  }

  HasString(haystack, needle){
    for (var i = 0; i < haystack.length; i++) {
      if (haystack[i] == needle) {
        return true;
      }
    }
    return false;
  }

  IsSubString(haystack, needle){
    if(haystack.lastIndexOf(needle) != -1){
      return true;
    }
    return false;
  }

  split(val) {
    return val.split( /,\s*/ );
  }

  extractLast(term) {
    return this.split( term ).pop();
  }

  Trem(needle){

    //remove the prepended spaces
    if (needle[0] == ' ') {
      var new_needle = '';
      for (var i = 0; i < needle.length; i++) {
        if (i != 0) {
          new_needle += needle[i];
        }
      }
      needle = this.Trem(new_needle);
    }

    //remove the appended spaces
    if (needle[needle.length - 1] == ' ') {
      var new_needle = '';
      for (var i = 0; i < needle.length; i++) {
        if (i != needle.length-1) {
          new_needle += needle[i];
        }
      }
      needle = this.Trem(new_needle);
    }
    return needle;
  }

  StringReplace(word, from, to) {
    var returnvalue = '';
    for(var x in word){
      if(word[x] == from){
        returnvalue += to;
        continue;
      }
      returnvalue += word[x];
    }
    return returnvalue;
  }
}
