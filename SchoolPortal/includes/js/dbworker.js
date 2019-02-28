this.onmessage = function (e) {
  if (e.data.table == '.admin') {
    e.data.table = '.staff';
  }
  if(e.data.action == "InsertIntoDb"){
    this.postMessage(InsertIntoDb(e.data.url, e.data.sql, e.data.table));
  }
  if(e.data.action == "CheckToInsert"){
    this.postMessage(CheckToInsert(e.data.url, e.data.sql, e.data.column, e.data.new_val, e.data.table));
  }
  if(e.data.action == "GetRowsFromTable"){
    this.postMessage(GetRowsFromTable(e.data.url, e.data.table, e.data.colname1, e.data.colvalue1, e.data.colname2, e.data.colvalue2, e.data.id));
  }
  if(e.data.action == "GetFromTable"){
    this.postMessage(GetFromTable(e.data.url, e.data.table, e.data.colname1, e.data.colvalue1, e.data.colname2, e.data.colvalue2, e.data.colreturn));
  }
  if(e.data.action == "GetColumnFromTable"){
    this.postMessage(GetColumnFromTable(e.data.url, e.data.table, e.data.colname1, e.data.colvalue1, e.data.colname2, e.data.colvalue2, e.data.colreturn));
  }
  if(e.data.action == "DoesRowsExist"){
    this.postMessage(DoesRowsExist(e.data.url, e.data.table, e.data.colnames, e.data.colvalues));
  }
  if(e.data.action == "DeleteFromDB"){
    this.postMessage(DoesRowsExist(e.data.url, e.data.table, e.data.column, e.data.group));
  }
  if(e.data.action == "RegisterStudent"){
    this.postMessage(RegisterStudent(e.data.url, e.data.email, e.data.user_name, e.data.password));
  }
  if(e.data.action == "AddStaff"){
    this.postMessage(AddStaff(e.data.url, e.data.email, e.data.open_date));
  }
  if (e.data.action == "MakeAdmin") {
    this.postMessage(MakeAdmin(e.data.url, e.data.e_key, e.data.owner));
  }
  if (e.data.action == "RemoveAdmin") {
    this.postMessage(RemoveAdmin(e.data.url, e.data.e_key, e.data.owner));
  }
  if(e.data.action == "AddClass"){
    this.postMessage(AddClass(e.data.url, e.data.title));
  }
  if(e.data.action == "AddClassSection"){
    this.postMessage(AddClassSection(e.data.url, e.data.section, e.data.class, e.data.form_teacher));
  }
  if(e.data.action == "AddSubject"){
    this.postMessage(AddSubject(e.data.url, e.data.title));
  }
  if(e.data.action == "AllocateSubject"){
    this.postMessage(AllocateSubject(e.data.url, e.data.class_section, e.data.teacher, e.data.subject));
  }
  if(e.data.action == "SetExam"){
    this.postMessage(SetExam(e.data.url, e.data.subject, e.data._class, e.data.academic_session, e.data.term, e.data.type, e.data.due, e.data.qanda));
  }
  if (e.data.action == "EditExam") {
    this.postMessage(EditExam(e.data.args))
  }
  if(e.data.action == "AddNewQuestion"){
    this.postMessage(AddNewQuestion(e.data.url, e.data.exam, e.data.question, e.data.question_number, e.data.a, e.data.b, e.data.c, e.data.d, e.data.correct));
  }
  if (e.data.action == "EditQANDA") {
    this.postMessage(EditQANDA(e.data.args))
  }
  if (e.data.action == "SubmitExam") {
    this.postMessage(SubmitExam(e.data.args))
  }
  if (e.data.action == "StoreExamSession") {
    this.postMessage(StoreExamSession(e.data.args))
  }
  if (e.data.action == "PublishResult") {
    this.postMessage(PublishResult(e.data.args))
  }
  if (e.data.action == "echo") {
    this.postMessage(e.data.message);
  }
}

function Ajax(method, url, async, data) {
  var returnval;
  var xhttp = new XMLHttpRequest();
  xhttp.open(method, url, async);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      returnval = xhttp.responseText;
    }
  }
  xhttp.send(data);
  return returnval;
}

function GetFromTable(url, table, colname1, colvalue1, colname2, colvalue2, colreturn) {
  var data = 'getfromtable='+table +'&colname1='+colname1 +'&colvalue1='+colvalue1 +'&colname2='+colname2 +'&colvalue2='+colvalue2 +'&colreturn='+colreturn;
  return JSON.parse(Ajax('POST', url, false, data));
}

function GetColumnFromTable(url, table, colname1, colvalue1, colname2, colvalue2, colreturn) {
  var data = 'getcolumnfromtable='+table +'&colname1='+colname1 +'&colvalue1='+colvalue1 +'&colname2='+colname2 +'&colvalue2='+colvalue2 +'&colreturn='+colreturn;
  return JSON.parse(Ajax('POST', url, false, data));
}

function GetRowsFromTable(url, table, colname1, colvalue1, colname2, colvalue2, id) {
  var data = 'getrowsfromtable='+table +'&colname1='+colname1 +'&colvalue1='+colvalue1 +'&colname2='+colname2 +'&colvalue2='+colvalue2 +'&id='+id;
  return JSON.parse(Ajax('POST', url, false, data));
}

function DoesRowsExist(url, table, colnames, colvalues) {
  var data = 'doesrowsexist='+table+'&colnames='+colnames+'&colvalues='+colvalues;
  return Ajax('POST', url, false, data);
}

function DeleteFromDB(url, table, column, group) {
  var data = 'deletefromdb='+table+'&column='+column+'&group='+group;
  return Ajax('POST', url, false, data);
}

function InsertIntoDb(url, sql, table) {
  var data = 'insertintodb='+sql+'&table='+table;
  return Ajax('POST', url, false, data);
}

function CheckToInsert(url, sql, column, new_val, table) {
  if (DoesRowsExist(url, table, [column], [new_val]) == 0) {
    return InsertIntoDb(url, sql, table);
  }else {
    return 'name_error';
  }
}

function RegisterStudent(url, email, user_name, password){
  //check if user_name and email exists
  returnval = '';
  if (DoesRowsExist(url, '.student', ['email'], [email]) == 0) {
    if (DoesRowsExist(url, '.student', ['user_name'], [user_name]) == 0) {
      data = "register_student=1"+"&email="+email+"&user_name="+user_name+"&password="+password;
      return Ajax('POST', url, false, data);
    }
    else {
      return "user_name_error";
    }
  }
  else {
    return 'email_error';
  }
}

function AddStaff(url, email, open_date){
  //check if email exists
  if (DoesRowsExist(url, '.staff', ['email'], [email]) == 0) {
    data = "add_staff=1"+"&email="+email+"&open_date="+open_date;
    return Ajax('POST', url, false, data);
  }
  else {
    return 'email_error';
  }
}

function MakeAdmin(url, e_key, owner) {
  data = "make_admin="+owner+"&e_key="+e_key;
  return Ajax('POST', url, false, data);
}

function RemoveAdmin(url, e_key, owner) {
  data = "remove_admin="+owner+"&e_key="+e_key;
  return Ajax('POST', url, false, data);
}

function AddClass(url, title){
  //check if class exists
  if (DoesRowsExist(url, '.classes', ['title'], [title]) == 0) {
    data = "add_class=1"+"&title="+title;
    return Ajax('POST', url, false, data);
  }
  else {
    return 'class_error';
  }
}

function AddClassSection(url, section, my_class, form_teacher){
  //check if class exists
  if (DoesRowsExist(url, '.class_sections', ['section', 'class'], [section, my_class]) == 0) {
    data = "add_class_section=1"+"&section="+section+"&class="+my_class+"&form_teacher="+form_teacher;
    return Ajax('POST', url, false, data);
  }
  else {
    return 'class_section_error';
  }
}

function AddSubject(url, title){
  //check if suject exists
  if (DoesRowsExist(url, '.subjects', ['title'], [title]) == 0) {
    data = "add_subject=1"+"&title="+title;
    return Ajax('POST', url, false, data);
  }
  else {
    return 'subject_error';
  }
}

function AllocateSubject(url, class_section, teacher, subject){
  //check if suject exists
  if (DoesRowsExist(url, '.allocated_subjects', ['subject', 'teacher', 'class_section'], [subject, teacher, class_section]) == 0) {
    data = "allocate_subject=1"+"&subject="+subject+"&teacher="+teacher+"&class_section="+class_section;
    return Ajax('POST', url, false, data);
  }
  else {
    return 'allocation_error';
  }
}

function SetExam(url, subject, _class, academic_session, term, type, due, qanda){
  //check if suject exists
  if (DoesRowsExist(url, '.exams', ['subject', 'class', 'academic_session', 'term', 'type', 'due'], [subject, _class, academic_session, term, type, due]) == 0) {
    data = "set_exam=1"+"&subject="+subject+"&type="+type+"&class="+_class+"&academic_session="+academic_session+"&term="+term+"&due="+due+"&qanda="+JSON.stringify(qanda);
    return Ajax('POST', url, false, data);
  }
  else {
    return 'allocation_error';
  }
}

function AddNewQuestion(url, owner, question, question_number, a, b, c, d, correct) {
  if (DoesRowsExist(url, '.qanda', ['owner', 'question', 'a', 'b', 'c', 'd', 'correct'], [owner, question, a, b, c, d, correct]) != 0) {
    return 'question_error';
  }
  if (DoesRowsExist(url, '.qanda', ['question_number'], [question_number]) != 0) {
    return 'number_error';
  }
  data = "add_new_question=1&owner="+owner+"&question="+question+"&question_number="+question_number+'&a='+a+'&b='+b+'&c='+c+'&d='+d+'&correct='+correct;
  return Ajax('POST', url, false, data);
}

function EditExam(args) {
  if (DoesRowsExist(args.url, '.exams', ['subject', 'class', 'academic_session', 'term', 'type', 'due'], [args.subject, args._class, args.academic_session, args.term, args.type, args.due]) == 0) {
    data = "edit_exam=1&id="+args.id+"&subject="+args.subject+"&type="+args.type+"&class="+args._class+"&academic_session="+args.academic_session+"&term="+args.term+"&due="+args.due;
    return Ajax('POST', args.url, false, data);
  }
  else {
    return 'allocation_error';
  }
}

function EditQANDA(args) {
  if(DoesRowsExist(args.url, '.qanda', ['question_number'], [args.question_number]) != 0 && GetFromTable(args.url, '.qanda', 'id', args.id, '', '', 'question_number') != args.question_number){
    return 'number_error';
  }
  else if (DoesRowsExist(args.url, '.qanda', ['question', 'correct', 'a', 'b', 'c', 'd'], [args.question, args.correct, args.a, args.b, args.c, args.d]) != 0) {
    return 'allocation_error';
  }
  else {
    data = "edit_qanda=1&id="+args.id+"&question_number="+args.question_number+"&question="+args.question+"&correct="+args.correct+"&a="+args.a+"&b="+args.b+"&c="+args.c+"&id="+args.id;
    return Ajax('POST', args.url, false, data);
  }
}

function SubmitExam(args) {
  data = "submit_exam=1&exam="+args.exam+"&owner="+args.owner+"&qanda="+JSON.stringify(args.qanda)+"&exam_session="+args.exam_session;
  return Ajax('POST', args.url, false, data);
}

function StoreExamSession(args) {
  data = "store_exam_session=&id="+args.id+"&remaining_time="+args.remaining_time+"&qanda="+JSON.stringify(args.qanda);
  return Ajax('POST', args.url, false, data);
}

function PublishResult(args) {
  data = "publish_result=&academic_session="+args.academic_session+"&term="+args.term;
  return Ajax('POST', args.url, false, data);
}
