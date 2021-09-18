<?php
    require_once '../../User.php';

    session_start();
    header('Content-type: application/json');

    // TODO: proper response codes...

    $response = array(
        'error' => TRUE,
        'message' => "Unauthenticated",
        'loggedin' => FALSE,
    );

    if (!empty($_SESSION["user"])) {
        $ftp = $_SESSION["user"];

        $response["message"] = "Directory is not defined in dir GET variable.";
        $response["loggedin"] = TRUE;

        if (!empty($_GET["dir"])) {
            $dir = $_GET["dir"];
            // Consider telling the dir that we are looking.
            $response["message"] = "Directory does not exist. Taking you to root dir.";    

            if ($dir === "..") {
                $current_dir = $ftp->current_dir();

                $dir = dirname($current_dir);
            }

            if ($ftp->change_dir($dir)) {
                $response["error"] = FALSE;
                $response["message"] = "Changed directory.";    
            } else {
                $ftp->change_dir("/");
            }
        }
    }

    echo json_encode($response);