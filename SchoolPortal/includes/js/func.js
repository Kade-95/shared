var root = window.location.href.match(/^.*\//),
    c_path = String(root).replace("http://127.0.0.1/", ""),
    returnvalue = '',
    cd = '',
    url = '',
    utk_url = '',
    url_vars = {},
    user = 'user',
    slideIndex = 0,
    full_url = window.location.href,
    notification_icon = $('<span/>').addClass('notification'),
    didScroll = false,
    lastScrollTop = 0,
    navBarHeight = $('header').outerHeight(),
    delta = 5,
    db_worker = '',
    header = $('header').html();

$(window).scroll(function() {
 didScroll = true;
});

setInterval(function() {
 if(didScroll){
   hasScrolled();
   didScroll = false;
 }
}, 250);

$(document).ready(function() {
  if (c_path.lastIndexOf('admin') != -1) {
    user = 'admin';
    cd = '../';
  }

  url = root+cd+'includes/php/func.php';
  utk_url = cd+'includes/js/utk.js';
  man_url = cd+'includes/js/StringManipulator.js',
  db_worker = cd+'includes/js/dbworker.js';

  $.importer(utk_url);
  $.importer(man_url);

  date = new Date();
  man = new StringManipulator(url, user, date);
  utk = new UTK(url, man);

  url_vars = utk.GetUrlVars(full_url);
//click handlers
{
  $('#add_login').click(function () {
    $('#login_form').removeClass('hide');
    $('#register_form').addClass('hide');
  });

  $('#add_register').click(function () {
    $('#login_form').addClass('hide');
    $('#register_form').removeClass('hide');
  });

  $('html').click(function (event) {
    id = event.target.id;
    if (id == 'sign_in') {
      $('#sign_in_as').slideToggle('slow')
    }
  });

  $('.nav_toggle').click(function () {
    $(this).toggleClass('nav_open').toggleClass('nav_close');
    $("#side_bar").slideToggle('slow');
  });

  $(document).on('click', '.toggle', function () {
    $(this).toggleClass('toggle_open').toggleClass('toggle_close');
    $(this).parent().parent().children('.expandable_content').slideToggle('slow');
  });

  $('tr').dblclick(function () {
    id = $(this).attr('id');
    _class = $(this).attr('class');

    if (_class == 'view_staff') {
      window.open('index.php?profile=staff&key='+id+'&owner=staff', '_self');
    }
    else if (_class == 'view_student') {
      window.open('index.php?profile=student&key='+id+'&owner=student', '_self');
    }
    else if (_class == 'view_classes') {
      window.open('index.php?classes=view&type='+id, '_self');
    }
    else if (_class == 'view_class_sections') {
      window.open('index.php?class_sections=view&type='+id, '_self');
    }
    else if (_class == 'view_subject') {
      window.open('index.php?subjects=view&type='+id, '_self');
    }
    else if (_class == 'view_exam') {
      window.open('index.php?exams=view&type='+id, '_self');
    }
    else if (_class == 'take_exam') {
      window.open('index.php?exams=take&type='+id, '_self');
    }
    else if (_class == 'marked_exam') {
      window.open('index.php?exams=marked&type='+id, '_self');
    }
  });

  $(document).on('click', '.change_status', function (e) {
    id = $(this).attr('id');
    if (id == 'make_admin') {
      $(this).text('Processing...').attr('type', '').css({'cursor':'auto'});
      if (window.Worker) {
        data_worker = new Worker(db_worker);
        data_worker.postMessage({url:url, action : 'MakeAdmin', e_key: $(this).attr('data-id'), owner: $(this).attr('data-type')});
        data_worker.onmessage = function (e) {
          if (e.data == '1') {
            utk.ShowUpdate("Admin Creation was successful");
            $('#make_admin').text('Remove Admin').attr('type', '').css({'cursor':'pointer'}).attr('id', 'remove_admin');
          }
          else if(e.data == '0'){
            utk.ShowUpdate("Admin Creation was unsuccessful");
            $('#make_admin').text('Make Admin').attr('type', '').css({'cursor':'pointer'});
          }
        }
      }
    }else if (id == 'remove_admin') {
      $(this).text('Processing...').attr('type', '').css({'cursor':'auto'});
      if (window.Worker) {
        data_worker = new Worker(db_worker);
        data_worker.postMessage({url:url, action : 'RemoveAdmin', e_key: $(this).attr('data-id'), owner: $(this).attr('data-type')});
        data_worker.onmessage = function (e) {
          if (e.data == '1') {
            utk.ShowUpdate("Admin Removal was successful");
            $('#remove_admin').text('Make Admin').attr('type', '').css({'cursor':'pointer'}).attr('id', 'make_admin');
          }
          else if(e.data == '0'){
            utk.ShowUpdate("Admin Removal was unsuccessful");
            $('#remove_admin').text('Remove Admin').attr('type', '').css({'cursor':'pointer'});
          }
        }
      }
    }
  });
}

  ShowSlide(0);
  TogglePassword();
  if (man.IsSubString(full_url, '?sign_in')) {
    RegisterStudent();
  }
  if (man.IsSubString(full_url, '?profile')) {
    EditProfile();
  }
  if (man.IsSubString(full_url, '?staff=add')) {
    AddStaff();
  }
  if (man.IsSubString(full_url, '?classes=add')) {
    AddClass();
  }
  if (man.IsSubString(full_url, '?classes=view')) {
    EditClass();
  }
  if (man.IsSubString(full_url, '?class_sections=add')) {
    AddClassSection();
  }
  if (man.IsSubString(full_url, '?class_sections=view')) {
    EditClassSection();
  }
  if (man.IsSubString(full_url, '?subjects=add')) {
    AddSubject();
  }
  if (man.IsSubString(full_url, '?subjects=allocate')) {
    AllocateSubject();
  }
  if (man.IsSubString(full_url, '?exams=set')) {
    SetExam();
  }
  if (man.IsSubString(full_url, '?exams=view')) {
    EditExam();
  }
  if (man.IsSubString(full_url, '?exams=take')) {
    TakeExam();
  }
  if (man.IsSubString(full_url, '?results=publish')) {
    PublishResult();
  }
});

//importer function
(function ($) {
  var imported = [];
  $.extend(true, {
    importer : function (script) {
      var found = false;
      for (var i = 0; i < imported.length; i++) {
        if (imported[i] == script) {
          found = true;
          break;
        }
      }

      if (!found) {
        $('body').append($('<script/>').attr('src', script));
        imported.push(script);
      }
    },
    work : function (args) {
      var def = $.Deferred(function(dfd) {
        var worker;
    		if (window.Worker) {
    			//Construct the Web Worker
          var worker = new Worker(args.file);
          worker.onmessage = function(event) {
    				//If the Worker reports success, resolve the Deferred
    				dfd.resolve(event.data);
    			};
    			worker.onerror = function(event) {
    				//If the Worker reports an error, reject the Deferred
    				dfd.reject(event);
    			};
    			worker.postMessage(args.args); //Start the worker with supplied args
        }else {
          //Nothing happens
        }
    	});
    	//Return the promise object (an "immutable" Deferred object for consumers to use)
    	return def.promise();
    }
  });
}(jQuery));

function TogglePassword() {
  var password = $('#password');
  var repassword = $('#repassword');
  $('#show_password').click(function (event) {
    event.preventDefault();
    var type = password.attr('type')

    var p_class = password.attr('class')
    var pid = password.attr('id')
    var pname = password.attr('name')
    var pval = password.val();

    var rep_class = repassword.attr('class')
    var repid = repassword.attr('id')
    var repname = repassword.attr('name')
    var repval = repassword.val();

    if (type == 'password') {
      new_password = $('<input/>').val(pval).attr({'type':'text', 'id':pid, 'name':pname}).addClass(p_class);

      new_repassword = $('<input/>').val(repval).attr({'type':'text', 'id':repid, 'name':repname}).addClass(rep_class);

      password.replaceWith(new_password);
      password = new_password;

      repassword.replaceWith(new_repassword);
      repassword = new_repassword;
      $(this).html('hide')
    }else if (type == 'text') {
      new_password = $('<input/>').val(pval).attr({'type':'password', 'id':pid, 'name':pname}).addClass(p_class);

      new_repassword = $('<input/>').val(repval).attr({'type':'password', 'id':repid, 'name':repname}).addClass(rep_class);

      password.replaceWith(new_password);
      password = new_password;

      repassword.replaceWith(new_repassword);
      repassword = new_repassword;
      $(this).html('show')
    }
  });
}

function ShowSlide(n) {
  var slides = $('#qoutes').children('p');
  $('#qoutes p').slideUp(2000);
  $('#qoutes').css('background-image', 'url(../images/background_'+slideIndex+'.png)');
  $('#qoutes p:eq('+slideIndex+')').slideDown(2000);
  slideIndex++;
  if (slideIndex == slides.length) {
    slideIndex = 0;
  }
  setTimeout(ShowSlide, 10000);
}

function hasScrolled () {
   var st = $(this).scrollTop();

  // Make sure they scroll more than delta
  if(Math.abs(lastScrollTop - st) <= delta)
      return;

   // If they scrolled down and are past the navbar, add class .nav-up.
   // This is necessary so you never see what is "behind" the navbar.
   if (st < lastScrollTop && st < navBarHeight){
     // Scroll Down
     $('.nav_up').hide('slow');
   }else if (st > lastScrollTop && st > navBarHeight) {
     $('.nav_up').hide('slow');
   }else if (st < navBarHeight) {
     $('.nav_up').hide('slow');
   }else {
       // Scroll Up
     if(st + $(window).height() < $(document).height()) {
       $('.nav_up').show('slow').html(header);
       $('.nav_up .tools').hide();
       $('.nav_up #notification_display').hide();
     }
   }

   lastScrollTop = st;
}

function RegisterStudent(){
  var form = $('#register_form'),
      user_name = $('#user_name'),
      email = $('#email');

  form.submit(function (event) {
    $('#submit').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    var password = $('#password'),
        repassword = $('#repassword');
    $('.error').hide();
    event.preventDefault();

    if(!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Register').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if(password.val() != repassword.val()){
      event.preventDefault();
      repassword.parent().find('.error').show();
      return;
    }else if(!man.IsPasswordValid(password.val())){
      $('#password_error').show().text("Password not valid");
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'RegisterStudent', email : email.val(), user_name : user_name.val(), password : password.val()});
      data_worker.onmessage = function (e) {
        $('#submit').text('Register').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Registration Successful. Login");
          window.open('index.php?my_profile', '_self');
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Registration Failed. Try again");
        }
      }
    }
  });
}

function EditProfile() {
  if ($('.profileimagediv').attr('data-owner') == 'admin') {
    return;
  }
  $('.editable p').click(function () {
    $(this).toggle();
    $(this).next().toggle().focus().val();
  });

  $('.edit').focusout(function () {
    $(this).toggle();
    p = $(this).prev();
    p.toggle();
    new_val = $(this).val();
    table = $(this).attr('data-table');
    column = $(this).attr('data-column');
    if (!utk.ValidateForm($(this).parent())) {
      utk.ShowUpdate("Not a valid input");
      return;
    }

    sql = "UPDATE *** SET "+column+"='"+new_val+"' WHERE e_key='"+utk.GetSession()+"'";
    if (column == 'user_name' || column == 'email') {
      $.work({file: db_worker, args:{url:url, action:"CheckToInsert", column:column, new_val: new_val, sql:sql, table:'.'+utk.GetType()} }).then(function (result) {
        if(result == '1'){
          p.text(new_val);
          utk.ShowUpdate('Updated');
        }else if (result == 'name_error') {
          utk.ShowUpdate('Already in use');
        }else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
    }
    else {
      $.work({file: db_worker, args:{url:url, action:"InsertIntoDb", sql:sql, table:'.'+utk.GetType()} }).then(function (result) {
        if(result == '1'){
          p.text(new_val);
          utk.ShowUpdate('Updated');
        }else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
    }
  });

  args = {url: url, action: 'GetColumnFromTable', table: '.countries', colname1: '', colvalue1: '', colname2: '', colvalue2: '', colreturn: 'name'};

  $.work({file: db_worker, args:args }).then(function (countries) {
      for(var x in countries){
        $('#nationality').append($("<option/>").html(countries[x]).attr('value', countries[x]));
      }
    }).fail(function (countries) {
      utk.ShowUpdate(countries)
    });

  $('#nationality').change(function () {
    $('#state').html($("<option/>").html("Select your state").attr('value', '0'));
    $('#city').html($("<option/>").html("Select your city").attr('value', '0'));
    country = $(this).val();

    args = {url: url, action: 'GetFromTable', table: '.countries', colname1: 'name', colvalue1: country, colname2: '', colvalue2: '', colreturn: 'country_key'};

    $.work({file: db_worker, args:args }).then(function (country_key) {

      args = {url: url, action: 'GetColumnFromTable', table: '.states', colname1: 'country_key', colvalue1: country_key, colname2: '', colvalue2: '', colreturn: 'name'};

      $.work({file: db_worker, args:args }).then(function (states) {
          for(var x in states){
            $('#state').append($("<option/>").html(states[x]).attr('value', states[x]));
          }
        }).fail(function (states) {
          utk.ShowUpdate(states)
        });

      }).fail(function (states) {
        utk.ShowUpdate(states)
      });
  });

  $('#state').change(function () {
    $('#city').html($("<option/>").html("Select your city").attr('value', '0'));
    state = $(this).val();

    args = {url: url, action: 'GetFromTable', table: '.states', colname1: 'name', colvalue1: state, colname2: '', colvalue2: '', colreturn: 'state_key'};

    $.work({file: db_worker, args:args }).then(function (state_key) {

      args = {url: url, action: 'GetColumnFromTable', table: '.cities', colname1: 'state_key', colvalue1: state_key, colname2: '', colvalue2: '', colreturn: 'name'};

      $.work({file: db_worker, args:args }).then(function (cities) {
          for(var x in cities){
            $('#city').append($("<option/>").html(cities[x]).attr('value', cities[x]));
          }
        }).fail(function (cities) {
          utk.ShowUpdate(cities)
        });

      }).fail(function (cities) {
        utk.ShowUpdate(cities)
      });
  });
}

function AddStaff(){
  var form = $('#add_staff'),
      email = $('#email'),
      open_date = $('#open_date');

  form.submit(function (event) {
    $('#submit').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Add staff').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'AddStaff', email : email.val(), open_date : open_date.val()});
      data_worker.onmessage = function (e) {
        $('#submit').text('Add staff').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Adding Successful");
          window.open('index.php?staff=show&type=all', '_self');
        }
        else if(e.data == 'email_error'){
          utk.ShowUpdate("Staff Already exists in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Adding Failed. Try again");
        }
      }
    }
  })
}

function AddClass(){
  var form = $('#add_class'),
      title = $('#title');

  form.submit(function (event) {
    $('#submit').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Add Class').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'AddClass', title : title.val()});
      data_worker.onmessage = function (e) {
        $('#submit').text('Add Class').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Adding Successful");
          window.open('index.php?classes=show&type=all', '_self');
        }
        else if(e.data == 'class_error'){
          utk.ShowUpdate("Class Already exists in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Adding Failed. Try again");
        }
      }
    }
  });
}

function EditClass() {
  $('.editable p').click(function () {
    $(this).toggle();
    $(this).next().toggle().focus().val();
  });

  $('.edit').focusout(function () {
    $(this).toggle();
    p = $(this).prev();
    p.toggle();
    new_val = $(this).val();
    table = $(this).attr('data-table');
    column = $(this).attr('data-column');
    if (!utk.ValidateForm($(this).parent())) {
      utk.ShowUpdate("Not a valid input");
      return;
    }

    sql = "UPDATE *** SET "+column+"='"+new_val+"' WHERE id="+$('#sub_menu').attr('data-class');
    if (column == 'title') {
      $.work({file: db_worker, args:{url:url, action:"CheckToInsert", column:column, new_val: new_val, sql:sql, table:table} }).then(function (result) {
        if(result == '1'){
          p.text(new_val);
          utk.ShowUpdate('Updated');
        }else if (result == 'name_error') {
          utk.ShowUpdate('Already in use');
        }else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
    }
    // else {
    //   $.work({file: db_worker, args:{url:url, action:"InsertIntoDb", sql:sql, table:'.'+utk.GetType()} }).then(function (result) {
    //     if(result == '1'){
    //       p.text(new_val);
    //       utk.ShowUpdate('Updated');
    //     }else {
    //       utk.ShowUpdate('Update unsuccessful');
    //     }
    //   }).fail(function (result) {
    //       utk.ShowUpdate(result)
    //   });
    // }
  });
}

function AddClassSection(){
  var form = $('#add_class_section'),
      section = $('#section'),
      form_teacher = $('#form_teacher');

  form.submit(function (event) {
    $('#submit').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Add Class').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'AddClassSection', section : section.val(), class: url_vars.type, form_teacher: form_teacher.val()});
      data_worker.onmessage = function (e) {
        $('#submit').text('Add Class Section').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Adding Successful");
          window.open('index.php?class_sections=show&type='+url_vars.type, '_self');
        }
        else if(e.data == 'class_section_error'){
          utk.ShowUpdate("Class Section Already exists in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Adding Failed. Try again");
        }
      }
    }
  });
}

function EditClassSection() {
  $('.editable p').click(function () {
    $(this).toggle();
    $(this).next().toggle().focus().val();
  });

  $('.edit').focusout(function () {
    $(this).toggle();
    p = $(this).prev();
    p.toggle();
    new_val = $(this).val();
    table = $(this).attr('data-table');
    column = $(this).attr('data-column');
    if (!utk.ValidateForm($(this).parent())) {
      utk.ShowUpdate("Not a valid input");
      return;
    }

    sql = "UPDATE *** SET "+column+"='"+new_val+"' WHERE id="+url_vars.type;
    if (column == 'section' || column == 'form_teacher') {
      $.work({file: db_worker, args:{url:url, action:"CheckToInsert", column:column, new_val: new_val, sql:sql, table:table} }).then(function (result) {
        if(result == '1'){
          if (column == 'form_teacher') {
            $.work({file: db_worker, args:{url: url, action: "GetFromTable", table: '.staff', colname1: 'e_key', colvalue1: new_val, colname2: '', colvalue2: '', colreturn: 'user_name'} }).then(function (result) {
              p.text(result);
            });
          }else {
            p.text(new_val);
          }
          utk.ShowUpdate('Updated');
        }else if (result == 'name_error') {
          utk.ShowUpdate('Already in use');
        }else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
    }
    // else {
    //   $.work({file: db_worker, args:{url:url, action:"InsertIntoDb", sql:sql, table:'.'+utk.GetType()} }).then(function (result) {
    //     if(result == '1'){
    //       p.text(new_val);
    //       utk.ShowUpdate('Updated');
    //     }else {
    //       utk.ShowUpdate('Update unsuccessful');
    //     }
    //   }).fail(function (result) {
    //       utk.ShowUpdate(result)
    //   });
    // }
  });
}

function AddSubject(){
  var form = $('#add_subject'),
      title = $('#title');

  form.submit(function (event) {
    $('#submit').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Add Class').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'AddSubject', title : title.val()});
      data_worker.onmessage = function (e) {
        $('#submit').text('Add Subject').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Adding Successful");
          window.open('index.php?subjects=show&type=all', '_self');
        }
        else if(e.data == 'subject_error'){
          utk.ShowUpdate("Subject Already exists in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Adding Failed. Try again");
        }
      }
    }
  });
}

function AllocateSubject(){
  var form = $('#allocate_subject'),
      class_section = $('#class_section'),
      teacher = $('#teacher');

  form.submit(function (event) {
    $('#submit').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Add Class').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'AllocateSubject', class_section : class_section.val(), teacher: teacher.val(), subject: url_vars.type});
      data_worker.onmessage = function (e) {
        $('#submit').text('Allocate Subject').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Allocation Successful");
          window.open('index.php?subjects=view&type='+url_vars.type, '_self');
        }
        else if(e.data == 'allocation_error'){
          utk.ShowUpdate("Subject Already allocated to class in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Allocation Failed. Try again");
        }
      }
    }
  });
}

function SetExam() {
  $(document).on('click', '.remove_question', function () {
    $(this).parents('.a_question').remove();
    var i = 0;
    $('.no').each(function () {
      $(this).text(i+1);
      i++;
    });
  });

  $('#add_question').click(function () {
    count = $('.a_question').length - 1;
    content_prototype = $('#content_prototype').clone().removeClass('hide').attr('id', '');
    $('#content_prototype').before(content_prototype);
    var i = 0;
    $('.no').each(function () {
      $(this).text(i+1);
      i++;
    });
    content_prototype.find('textarea').focus();
  });

  var form = $("#set_exam"),
      subject = $('#subject'),
      _class = $('#class'),
      academic_session = $('#academic_session'),
      term = $('#term'),
      type = $('#type'),
      due = $('#due');

  form.submit(function (event) {
    event.preventDefault();

    $('#submit').text('Processing').attr('type', '').css({'cursor':'auto'});

    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#submit').text('Set Exam').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if($('.a_question').length - 1 == 0){
      utk.ShowUpdate('No questions has been set, Click on add question to set one');
      $('#submit').text('Set Exam').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    var i = 0,
    qanda = [];

    $('.a_question').each(function () {
      id = $(this).attr('id');
      if (id != 'content_prototype') {
        question = $(this).find('textarea').attr({'id':'question_'+i, 'name':'question_'+i});
        question_number = $(this).find('.no').text(i+1);
        a = $(this).find('.a').attr({'id':'a_'+i, 'name':'a_'+i});
        b = $(this).find('.b').attr({'id':'b_'+i, 'name':'b_'+i});
        c = $(this).find('.c').attr({'id':'c_'+i, 'name':'c_'+i});
        d = $(this).find('.d').attr({'id':'d_'+i, 'name':'d_'+i});
        correct = $(this).find('.correct').attr({'id':'correct_'+i, 'name':'correct_'+i});
        qanda.push({'question': question.val(), 'question_number': question_number.text(), 'a': a.val(), 'b': b.val(), 'c': c.val(), 'd': d.val(), 'correct': correct.val()})
      }
      i++;
    });

    i = 0;

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({url:url, action : 'SetExam', subject : subject.val(), _class: _class.val(), academic_session: academic_session.val(), term: term.val(), type: type.val(), due: due.val(), qanda: qanda});
      data_worker.onmessage = function (e) {
        utk.ShowUpdate(e.data);
        $('#submit').text('Set Exam').attr('type', '').css({'cursor':'pointer'});
        if (e.data == '1') {
          utk.ShowUpdate("Exam Setting Successful");
          window.open('index.php?exams=show&type=all', '_self');
        }
        else if(e.data == 'allocation_error'){
          utk.ShowUpdate("Exam Already in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Allocation Failed. Try again");
        }
      }
    }
  });
}

function EditExam() {
  var form = $('#add_new_question'),
      question_number = $('#question_number'),
      question = $('#question'),
      a = $('#a'),
      b = $('#b'),
      c = $('#c'),
      d = $('#d'),
      correct = $('#correct');

  form.submit(function (event) {
    $('#submit').text('Processing').attr('type', '').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm($(this).parent())) {
      utk.ShowUpdate("Not a valid input");
      $('#submit').text('Add New Question').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    $.work({file: db_worker, args:{url:url, action:"AddNewQuestion", exam: url_vars.type, question:question.val(), question_number: question_number.val(), a: a.val(), b: b.val(), c: c.val(), d: d.val(), correct: correct.val()}}).then(function (result) {
      $('#submit').text('Add New Question').attr('type', '').css({'cursor':'pointer'});
        if(result == '1'){
          utk.ShowUpdate('Question Added, Exam Updated');
          window.open(full_url, '_self');
        }else if (result == 'question_error') {
          utk.ShowUpdate('Question already in this particular exam');
        }else if (result == 'number_error') {
          utk.ShowUpdate('There is a question with the same number');
        }else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
  });

  $("#update_exam").click(function () {
    $(this).text('Processing').css({'cursor':'auto'})
    if (!utk.ValidateForm($('#exam'))) {
      utk.ShowUpdate("Not a valid input");
      return;
    }
    var id = url_vars.type,
        subject = $("#subject"),
        _class = $("#class"),
        academic_session = $("#academic_session"),
        term = $("#term"),
        type = $("#type"),
        due = $('#due');

    $.work({file: db_worker, args:{action:"EditExam", args: {url:url, id: id, subject: subject.val(), _class: _class.val(), academic_session: academic_session.val(), term: term.val(), type: type.val(), due: due.val()}} }).then(function (result) {
      $('#update_exam').text('Update Exam').css({'cursor':'pointer'})
        if(result == '1'){
          utk.ShowUpdate('Exam Updated');
          window.open(full_url,'_self');
        }else if (result == 'allocation_error') {
          utk.ShowUpdate('Exam Already in Database');
        }
        else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
  });

  $(".update_qanda").click(function () {
    qanda = $(this).parents('.qanda');
    $(this).text('Processing').css({'cursor':'auto'});
    if (!utk.ValidateForm(qanda)) {
      utk.ShowUpdate("Not a valid input");
      return;
    }

    var id = qanda.parents('.a_question').attr('id'),
        question_number = qanda.parents('.a_question').find('.question_number'),
        question = qanda.find('.question'),
        correct = qanda.find('.correct'),
        a = qanda.find('.a'),
        b = qanda.find('.b'),
        c = qanda.find('.c'),
        d = qanda.find('.d');

    $.work({file: db_worker, args:{action:"EditQANDA", args: {url:url, id: id, question_number: question_number.val(), question: question.val(), correct: correct.val(), a: a.val(), b: b.val(), c: c.val(), d: d.val()}} }).then(function (result) {
      $('.update_qanda').text('Update Q&A').css({'cursor':'pointer'});
        if(result == '1'){
          utk.ShowUpdate('Q&A Updated');
          window.open(full_url,'_self');
        }else if (result == 'allocation_error') {
          utk.ShowUpdate('Question and Answers Already in Database');
        }
        else {
          utk.ShowUpdate('Update unsuccessful');
        }
      }).fail(function (result) {
          utk.ShowUpdate(result)
      });
  });
}

function TakeExam() {
  id = $("#exam_paper").attr('data-session');
  min_time = $('#remaining_time').find('#min');
  sec_time = $('#remaining_time').find('#sec');
  minutes = $('#remaining_time').find('#minutes');

  number = $('.question').length;
  min = 60;
  period = $('#remaining_time').attr('data-time');

  run_exam = setInterval(function () {
    if (!(period%30)) {
      qanda = [];
      $('.question').each(function () {
        question_number = $(this).attr('data-number');
        answer = '';
        $(this).find('.answer input').each(function () {
          if (this.checked) {
            answer = $(this).attr('data-name')
          }
        });
        qanda.push({question_number: question_number, answer: answer});
      });

      $.work({file: db_worker, args:{action:"StoreExamSession", args: {url:url, id: $('#remaining_time').attr('data-exam_session'), qanda: qanda, remaining_time: period}}}).then(function (result) {
        }).fail(function (result) {
            utk.ShowUpdate(result)
        });
    }
    --period;
    mins = new String(Math.floor(period/min));
    mins = (mins.length > 1) ? mins:'0'+mins;

    secs = new String(period%min);
    secs = (secs.length > 1) ? secs:'0'+secs;

    min_time.text(mins);
    sec_time.text(secs);

    if (period == min) {
      utk.ShowUpdate("Time is almost up. Hurry!!!");
      minutes.text('Minute');
    }

    if (!period) {
      alert('Time Up!!!');
      SubmitExam()
    }
  }, 1000);

  $('.answer input').click(function () {
    $(this).parents('.question').find('.answer input').each(function () {
      this.checked = false;
    })
    this.checked = true;
  });

  $('#submit').click(SubmitExam);

  function SubmitExam() {
    clearTimeout(run_exam);
    $("#submit").text('Processing...').attr('type', '').css({'cursor':'auto'});
    qanda = [];
    $('.question').each(function () {
      question_number = $(this).attr('data-number');
      answer = '';
      $(this).find('.answer input').each(function () {
        if (this.checked) {
          answer = $(this).attr('data-name')
        }
      });
      qanda.push({question_number: question_number, answer: answer});
    });

    $.work({file: db_worker, args:{action:"SubmitExam", args: {url:url, exam: url_vars.type, owner: $('header').attr('data-session'), qanda: qanda, exam_session: id}}}).then(function (result) {
      $('#submit').text('submit').attr('type', '').css({'cursor':'pointer'});
        if(result == '1'){
          utk.ShowUpdate('Exam Submitted Successfully. Result will be out in a while');
          window.open('index.php?exams=marked&type='+url_vars.type, '_self');
        }else if (result == 'exam_error') {
          utk.ShowUpdate('You have taken this particular exam before');
        }else {
          utk.ShowUpdate('Submission unsuccessful');
        }
      }).fail(function (result) {
          $('#submit').text('submit').attr('type', '').css({'cursor':'pointer'});
          utk.ShowUpdate(result)
      });
  }
}

function PublishResult() {
  var form = $('#publish_result'),
      academic_session = $('#academic_session'),
      term = $('#term');

  form.submit(function (event) {
    $('#publish').text('Processing...').attr('type', 'button').css({'cursor':'auto'});
    event.preventDefault();
    if (!utk.ValidateForm(form)) {
      utk.ShowUpdate('You must fill the required forms correctly');
      $('#publish').text('Publish Result').attr('type', '').css({'cursor':'pointer'});
      return;
    }

    if (window.Worker) {
      data_worker = new Worker(db_worker);
      data_worker.postMessage({action : 'PublishResult', args: {url:url, academic_session : academic_session.val(), term: term.val()}});
      data_worker.onmessage = function (e) {
        $('#publish').text('Publish Result').attr('type', '').css({'cursor':'pointer'});
        alert(e.data);
        return ;
        if (e.data == '1') {
          utk.ShowUpdate("Result Publishing Successful");
          window.open('index.php?results=show&type=all', '_self');
        }
        else if(e.data == 'result_error'){
          utk.ShowUpdate("Result Already has been published in database");
        }
        else if(e.data == '0'){
          utk.ShowUpdate("Publishing Failed. Try again");
        }
      }
    }
  });
}
{
  // function ChangeImage() {
//   var form = $('<form/>').attr({'enctype':'multipart/form-data', 'action':'', 'method':'post'}).css({'width':'50%', 'z-index':'1', 'position':'fixed', 'top':'0', 'margin-left':'25%'}).addClass('form'),
//       input = $('<input/>').attr({'type':'file'}).attr('name', 'image'),
//       upload = $('<button/>').html('upload'),
//       cancel = $('<button/>').html('cancel').attr('type', 'button').css('margin-right', '20%');
//
//   $('body').append(form.append(input, $('<br><br>'), upload, cancel)).css({'z-index':'-99', 'background':'rgba(225,225,225,1)'});
//
//   var image = 'driver/'+GetSession()+'/'+GetFromTable('SIMS.driver', 'email', GetSession(), '', '', 'image');
//
//   preview_image('.form input', $('.profileimagediv img'), "images/no_preview.png");
//
//   form.submit(function (event) {
//     event.preventDefault();
//     if (isImageValid(input.val())) {
//       form.append($('<input/>').attr('name', 'changeimage').css('display', 'none'));
//       $.ajax({
//         method: 'post',
//         url: url,
//         async: false,
//         data: new FormData(this),
//         processData: false,
//         contentType: false
//       }).done(function (data) {
//         if (data) {
//           form.remove()
//           $('body').css({'z-index':'1', 'background':'rgba(225,225,225,0)'});
//         }
//       });
//     }
//   })
//   cancel.click(function () {
//     image = 'driver/'+GetSession()+'/'+GetFromTable('SIMS.driver', 'email', GetSession(), '', '', 'image');
//     form.remove()
//     $('body').css({'z-index':'1', 'background':'rgba(225,225,225,0)'});
//     $('.profileimagediv img').attr('src', image);
//   })
// }

// function CheckNotification() {
//   notifications = GetColumnFromTable('SIMS.notification', 'user', GetSession(), 'status', '0', 'message');
//   if (notifications.length > 0) {
//     $('#notifications').append($('<span/>').addClass('notification').html(notifications.length));
//   }
// }
}
