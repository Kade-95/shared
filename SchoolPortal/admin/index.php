<?php
  include "../includes/php/func.php";
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Cab: <?php echo $man->ToUpperCase($user); ?></title>
    <link rel="stylesheet" href="../css/main_display.css">
    <link rel="stylesheet" href="../css/side_bar.css">
    <link rel="stylesheet" href="../css/main_window.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/landing.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/chat.css">
    <link rel="stylesheet" href="../css/task.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../includes/js/jqueryui/jquery-ui.min.css">
  </head>
  <body>
    <div class="cover">

    </div>
    <?php include_once $root.'/includes/php/pages/header.php'; ?>
    <?php include_once $root.'/includes/php/pages/main.php'; ?>
    <?php include_once $root.'/includes/php/pages/footer.php'; ?>

    <script src="../includes/js/jquery-3.2.0.min.js"></script>
    <script src="../includes/js/jqueryui/jquery-ui.js"></script>
    <script src="../includes/js/func.js"></script>
  </body>
</html>
