<?php
  $action = '';
  $type = '';

  if (isset($_GET['results'])) {
    $action = $_GET['results'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  ?>
    <br>
    <div class="">
      <a href="index.php?results=publish" class="button_link left_align">Publish Result</a>
    </div>
    <br><br><br><br>
  <?php

  if ($action == 'show') {
    $results = $query->GetRowsFromTable($site.'.results', '', '', '', '', 'id');
    ?>
    <br><br>
    <table class="table">
      <h2 class="title"> <?php echo $type ?> Results</h2>
      <tr>
        <th>S/N</th>
        <th>Class</th>
        <th>Academic Session</th>
        <th>Term</th>
        <th>Published On</th>
      </tr>
    </table>
    <?php
  }
  if ($action == 'view') {

  }
  if ($action == 'publish') {
    ?>
    <form class="form" action="" id="publish_result" method="post">
      <h2 class="title">Publish New Result</h2>
      <div>
        <label for="">Academic Session</label>
        <select id="academic_session">
          <?php
            foreach ($utk->FetchAcademicSession(5) as $key => $value) {
              ?>
              <option value="<?php echo ($key) ? $value:$key; ?>"><?php echo $value ?></option>
              <?php
            }
           ?>
        </select>
      </div>
      <div>
        <label for="">Term</label>
        <select id="term">
          <?php
            foreach ($utk->FetchTerms(5) as $key => $value) {
              ?>
              <option value="<?php echo $key; ?>"><?php echo $value ?></option>
              <?php
            }
           ?>
        </select>
      </div>
      <div class="submit">
        <button id="publish">Publish Result</button>
      </div>
    </form>
    <?php
  }
 ?>
