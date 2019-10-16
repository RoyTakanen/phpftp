<?php
// Start the session
session_start();
if ($_SESSION["currentdirectory" === ""]) {
  $_SESSION["currendirectory"] = ".";
}

/*Create functions*/

function cleanurl() {
  $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $cleanurl =preg_replace('/\\?.*/', '', $url);
  echo "<script>window.location = '$cleanurl';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Online FTP</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <ul class="navbar-nav">
    <li class="nav-item active">
      <a class="nav-link" href="https://kaikkitietokoneista.net">Kaikkitietokoneista.net</a>
    </li>
    <li class="nav-item">
      <form method="POST">
      	<input type="text" name="server" placeholder="ftpserver.com" required>
      	<input type="text" name="username" placeholder="Username" required>
      	<input type="password" name="password" placeholder="Password">
        <select name="method">
          <option value="ftp">FTP</option>
          <option value="ftps">FTPS</option>
        </select>
      	<input type="Submit" value="Login">
      </form>
    </li>
    <li>
    <?php if ($_SESSION["username"] != "") { ?>
      <a class="nav-link text-right" href="?data=logout">Log out</a>
    <?php } ?>
    </li>
  </ul>
</nav>

<div class="container">
  <?php
    if ($_POST["username"] != "") {
      $ftp_server = $_POST["server"];
      $ftp_username = $_POST["username"];
      $ftp_userpass = $_POST["password"];
      //echo $ftp_username."1";
    }

    else {
      $ftp_server = $_SESSION["server"];
      $ftp_username = $_SESSION["username"];
      $ftp_userpass = $_SESSION["userpass"];
      //echo $ftp_username."2";
    }

    $_SESSION["server"] = $ftp_server;
    $_SESSION["username"] = $ftp_username;
    $_SESSION["userpass"] = $ftp_userpass;
    //echo $ftp_username."4";

    // connect and login to FTP server
    if ($ftp_server != "") {
      if ($_POST["method"] == "ftp") {
        $ftp_conn = ftp_connect($ftp_server) or die("Could not create FTP connection to: " . htmlentities($ftp_server));
      } else {
        $ftp_conn = ftp_ssl_connect($ftp_server) or die("Could not creat FTPS connection to: " . htmlentities($ftp_server));
      }
      $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
      if ($login) {
        echo "Connected to: " . htmlentities($ftp_server) . "<br>Logged in as: ". htmlentities($ftp_username) . "<hr>"; //. "<br><hr>Current dir: ". ftp_pwd($ftp_conn);
        echo "<br><form method='GET'><input type='hidden' name='data' value='create'><input type='text' name='targetfile' value='New_file.html'><input type=\"submit\" value=\"Create\"></form>";
        echo "<br><form method='GET'><input type='hidden' name='data' value='makedir'><input type='text' name='targetdir' value='New_directory'><input type=\"submit\" value=\"Make dir\"></form>";
        echo "<br><form method='GET'><input type='hidden' name='data' value='upload'><input type='file' name='targetfile'><input type=\"submit\" value=\"Upload\"></form>";
        //Sets default dir
        if ($_SESSION["currendirectory"] != ".") {
          $defaultdir = $_SESSION["currendirectory"];
        }
        else {
          $defaultdir = ".";
        }

        //Dir change method
        if ($_GET["data"] === "changedir") {
          $defaultdir = $_GET["targetdir"];
          $_SESSION["currendirectory"] = $_GET["targetdir"];
        }

        //Nykyisen kansion listaus
        $file_list = ftp_nlist($ftp_conn, $defaultdir);
        echo "<table class=\"table table-striped\">
                <thead>
                  <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th><i class=\"fas fa-trash\"></i></th>
                    <th>Rename</th>
                    <th>Download</th>
                  </tr>
                </thead>
              <tbody>";
        foreach($file_list as $x => $x_value) {
          echo "<tr>";
          //Estaa . ja .. hakemistojen poistamisen
          echo "<td>" . $x . "</td><td><form method='GET'><input type='hidden' name='data' value='changedir'><input type='hidden' name='targetdir' value='" . $x_value . "'><input type=\"submit\" value=\"" . $x_value . "\"\"></form></td><td><a href=\"?data=remove&targetfile=".$x_value."\">Delete</a></td>
          <td><form method='GET'><input type='hidden' name='data' value='rename'><input type='hidden' name='targetfile' value='".$x_value."'><input type='text' name='renamedfile'><input type=\"submit\" value=\"Rename\"></form></td><td><a href='".$x_value."' download>".$x_value."</a></td>";
          echo "</tr>";
        }
        echo "</tbody></table>";

        //Tiedoston poistaminen
        if ($_GET["data"] === "remove") {
          $file = $_GET["targetfile"];
          if (ftp_delete($ftp_conn, $file)) {
            echo "$file deleted";
            cleanurl();
           }
          else {
            echo "Could not delete $file";
          }
        }
      //Tiedoston uudelleennimeaminen
      if ($_GET["data"] === "rename") {
        $old_file = $_GET["targetfile"];
        $new_file = $_GET["renamedfile"];

        // try to rename $old_file to $new_file
        if (ftp_rename($ftp_conn, $old_file, $new_file)) {
          echo "Renamed $old_file to $new_file";
          cleanurl();
        }
        else {
          echo "Problem renaming $old_file to $new_file";
        }
      }

      //Log out
      if ($_GET["data"] === "logout") {
        // remove all session variables
        session_unset();
        // destroy the session
        session_destroy();
        cleanurl();
      }

        //Create file
      if ($_GET["data"] === "create") {
        //Remember to plus current directory ($defaultdir) to targetfile
        $file = fopen($defaultdir . "/" . $_GET["targetfile"],"w");
        echo ftp_fput($ftp_conn, $_GET["targetfile"], "");
        fclose($fp);
        //cleanurl();
      }

        //Upload file
        if ($_GET["data"] === "upload") {
          $file = $_GET["targetfile"];
          $fp = fopen($file,"r");
          $file = $defaultdir . "/" . $_GET["targetfile"];
          if (ftp_fput($ftp_conn, $file, $fp, FTP_ASCII))
            {
            echo "Successfully uploaded $file.";
            cleanurl();
            }
          else
            {
            echo "Error uploading $file.";
            }
          fclose($fp);
        }

        //Directory making
        if ($_GET["data"] === "makedir") {
          $dir = $defaultdir . "/" . $_GET["targetdir"];
          // try to create directory $dir
          if (ftp_mkdir($ftp_conn, $dir))
            {
            echo "Successfully created $dir";
            cleanurl();
            }
          else
            {
            echo "Error while creating $dir";
            }
        }
        // close connection
        ftp_close($ftp_conn);
      } if (!$login) {
        echo "Login failed";
      }
    }
  ?>
</div>

</body>
</html>
