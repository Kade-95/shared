<?php
  $action = '';
  $type = '';

  if (isset($_GET['results'])) {
    $action = $_GET['results'];
  }

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  }

  if ($action == 'show') {
    $results = $query->GetRowsFromTable($site.'.results', '', '', '', '', 'id');
    ?>
    <br><br>
    <table class="table">
      <h2 class="title">My Results</h2>
      <tr>
        <th>S/N</th>
        <th>Class</th>
        <th>Academic Session</th>
        <th>Term</th>
        <th>Total</th>
        <th>Average</th>
        <th>Rating</th>
      </tr>
    </table>
    <?php
  }
  if ($action == 'view') {

  }
 ?>
