<?php
  spl_autoload_register(function ($class) {
    $filename = $class.'.php';
    include $filename;
  });
 ?>
