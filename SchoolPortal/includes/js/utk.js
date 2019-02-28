class UTK {
  constructor(url) {
  }

  GetElementProperty(element){
    var dx = 0,
        dy = 0,
        width = element.width()|0,
        height = element.height()|0;

    dx += element.position().left;
    dy += element.position().top;
    return {top: dy, left: dx, width: width, height: height};
  }

  isImageValid(input) {
    var ext = input.substring(input.lastIndexOf('.') + 1).toLowerCase();
    if(ext == "png" || ext == "gif" || ext == "jpeg" || ext == "jpg"){
      return true;
    }else {
      return false;
    }
  }

  preview_image(img, upload, no_preview = 'images/no_preview.png') {
    if (user == 'admin') {
      no_preview = '../'+no_preview;
    }
    $(img).change(function() {
      var input = $(this).val();
      var ext = input.substring(input.lastIndexOf('.') + 1).toLowerCase();
      if((ext == "png" || ext == "jpg" || ext == "jpeg" || ext == "gif") && this.files && this.files[0]){
        var reader = new FileReader();

        reader.onload = function (e) {
          $(upload).attr("src", e.target.result);
          $(upload).show();
        }

        reader.readAsDataURL(this.files[0]);
      }else {
        $(upload).attr("src", no_preview);
        $(upload).show();
      }
    });
  }

  GetSession() {
    return $('header').attr('data-session');
  }

  GetType() {
    return $('header').attr('data-type');
  }

  isFolder(file) {
    var returnvalue = '';
    $.ajax({
      method: 'post',
      url: this.url,
      async: false,
      data:{isFolder: file},
      datatype: "json",
    }).done(function(data) {
      returnvalue = data;
    });
    return returnvalue;
  }

  isFolderName(name) {
    if (this.IsSpaceString(name)) {
      alert("space string error");
      return false;
    }

    if (name == '') {
      alert("empty string error");
      return false;
    }

    var specialcharacters = "'\\/:?*<>|!_";
    for (var i = 0; i < specialcharacters.length; i++) {
      for (var j = 0; j < name.length; j++) {
        if (specialcharacters[i] == name[j]) {
          alert("'\\/?:<>|!_   are not allowed");
          return false;
        }
      }
    }
    return true;
  }

  HasClass(object, _class) {
    return man.IsSubString(object.attr('class'), _class);
  }

  converToRealPath(path) {
    if (path[path.length-1] != '/') {
      path += '/';
    }
    return path;
  }

  CopyFiles(source, destination) {
    var contents = [];
    for (var i in source){
      var new_source = [], new_destination = [];
      if (isFolder(source[i]) == 'folder') {
        source[i] = this.converToRealPath(source[i]);
        destination[i] = this.converToRealPath(destination[i]);
        contents = this.GetFolderContents(source[i]);
        if(this.MakeFolder(destination[i])){
          for(var j in contents){
             new_source.push(source[i]+contents[j].name);
             new_destination.push(destination[i]+contents[j].name);
          }
          CopyFiles(new_source, new_destination);
        }
      }else if(isFolder(source[i] == 'file')){
        $.ajax({
          method: 'post',
          url: this.url,
          async: false,
          data:{copyFile: 1, source: source[i], destination: destination[i]},
        }).done(function(data){

        });
      }
    }
  }

  RenameExisting(name) {
    if (isFolder(name) == 'folder') {
      name = RenameExisting(name+=1);
    }
    if (isFolder(name) == 'file') {
      last = name.substring(name.lastIndexOf('.') + 1);
      first = name.substring(0, name.lastIndexOf('.'));
      name = RenameExisting(first+'1.'+last);
    }
    return name;
  }

  DeleteFiles(targets) {
    for (var i = 0; i < targets.length; i++) {
      $.ajax({
        method: 'post',
        url: this.url,
        async: false,
        data:{deleteFiles: targets[i]},
      }).done(function (data) {
        rv = data;
      });
    }
    return rv;
  }

  ReplaceFolder(name) {
    if (this.DeleteFiles(name)) {
      if (this.MakeFolder(name)) {
        return true;
      }
    }
    return false;
  }

  MakeFolder(name) {
    $.ajax({
      method: 'post',
      url: this.url,
      async: false,
      data:{createFolder: name},
    }).done(function (data) {
      rv = data;
    });
    return rv;
  }

  CreateNewFile(name) {
    var rv;
    $.ajax({
      method: 'post',
      url: this.url,
      async: false,
      data:{createFile: name},
    }).done(function (data) {
      rv = data;
    });
    return rv;
  }

  //Get details of folder content
  GetFolderContents(path) {
    var contents;
    $.ajax({
      method: 'post',
      url: this.url,
      async: false,
      data:{folderClicked: path},
      datatype: "json",
    }).done(function(data) {
      contents = $.parseJSON(data);
    });
    return contents;
  }

  jq(value) {
    var new_value = '';
    for (var j = 0; j < value.length; j++) {
      if (this.is_spacial_character(value[j])) {
        new_value += '\\';
      }
      new_value += value[j];
    }
    return new_value;
  }

  is_spacial_character(char) {
    var specialcharacters = "'\\/:?*<>|!.";
    for (var i = 0; i < specialcharacters.length; i++) {
      if (specialcharacters[i] == char) {
        return true;
      }
    }
    return false;
  }

  GetServerRoot() {
    var rv;
    $.ajax({
      method: 'post',
      url: this.url,
      async: false,
      data:{getRoot: 1},
      datatype: "json",
    }).done(function(data) {
      rv = data;
    });
    return rv;
  }

  CountChar(haystack, needle) {
    var j = 0;
    for (var i = 0; i < haystack.length; i++) {
      if (haystack[i] == needle) {
        j++;
      }
    }
    return j;
  }

  ValidateFormTextarea(form){
    var returnvalue = true;
    form.find('textarea').each(function () {
      $(this).css('border-color', '#6200EA');
      if ($(this).parents('#content_prototype').attr('id') == 'content_prototype') {
        return true;
      }
      else if ($(this).val() == '') {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
    });
    return returnvalue;
  }

  ValidateFormInputs(form) {
    var returnvalue = true;
    var self = this;
    form.find('input').each(function () {
      $(this).css('border-color', '#6200EA');
      var type =  $(this).attr('type');
      var value = $(this).val();
      if ($(this).parents('#content_prototype').attr('id') == 'content_prototype') {
        return true;
      }
      else if (type == 'file' && value == '') {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
      else if (type == 'text' && (man.IsSpaceString(value) || value == '')) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
      else if (type == 'date' && !man.IsDateValid(value)) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
      else if (type == 'email' && !man.IsEmail(value)) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
      else if (type == 'number' && !man.IsNumber(value)) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
      else if (type == 'password' && !man.IsPasswordValid(value)) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
    });
    return returnvalue;
  }

  ValidateFormSelects(form) {
    var returnvalue = true;
    form.find('select').each(function () {
      $(this).css('border-color', '#6200EA');
      if ($(this).parents('#content_prototype').attr('id') == 'content_prototype') {
        return true;
      }
      else if ($(this).val() == 0 || $(this).val() == null) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
    });
    return returnvalue;
  }

  ValidateForm(form){
    return this.ValidateFormInputs(form) && this.ValidateFormSelects(form) && this.ValidateFormTextarea(form);
  }

  ValidateFormImages(form){
    var returnvalue = true;
    self = this;
    form.find('input').each(function () {
      $(this).css('border-color', '#6200EA');
      if ($(this).parents('#content_prototype').attr('id') == 'content_prototype') {
        return true;
      }
      else if (type == 'file' && !self.isImageValid(value)) {
        $(this).css('border-color', 'red');
        returnvalue = false;
        $(this).focus();
        return false;
      }
    });
    return returnvalue;
  }

  PrintContent(content, button) {
    var restore = $('body').html();
    button.hide();
    $('body').html(content);
    window.print();
    $('body').html(restore);
    button.show();
  }

  GetUrlVars(location){
    if (man.HasString(location, '?')) {
      var vars = location.split('?')[1];
      vars = vars.split('&');
      returnvalue = {};
      for(var x in vars){
        var parts = vars[x].split('=');
        returnvalue[parts[0]] = parts[1];
      }
      return returnvalue;
    }
  }

  ShowUpdate(update){
    $('#alert').hide('slow');
    $('#alert').show('slow').text(update);
    setTimeout(function () {
      $('#alert').hide('slow');
    }, 3000);
  }

  // FetchAcademicSession(years){
  //   current = '';
  //   sessions = ['Select Academic Session'];
  //   for (i=0; i < years; i++) {
  //     first = current+i;
  //     second = first+1;
  //     sessions[] = first+"/"+second;
  //   }
  //   return sessions;
  // }

  FetchTerms(){
    return ['Select Term', 'First Term', 'Second Term', 'Third Term'];
  }

  FetchExamTypes(){
    return ['Select Exam Types', 'First Assignment', 'Second Assignment', 'Third Assignment', 'Forth Assignment', 'Quiz', 'Exam'];
  }
}
