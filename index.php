<?php
  session_start();
  $LOGGED_IN = FALSE;

  if (isset($_SESSION["user"])) {
    $LOGGED_IN = TRUE;
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>phpftp - Online ftp</title>
  <!-- Compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<body>
  <?php
    if ($LOGGED_IN) {
      ?>
        <a href="/logout.php" class="waves-effect waves-light btn">Log out</a>
      <?php

      require 'partials/ftp/files.php';
    } else {
      require 'partials/login.php';
    }
  ?>
</body>
</html>