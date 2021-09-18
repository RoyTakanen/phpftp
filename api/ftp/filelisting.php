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

        $response["error"] = FALSE;
        $response["message"] = "Listing files in the current directory.";
        $response["loggedin"] = TRUE;

        if (!empty($_GET["dir"])) {
            $dir = $_GET["dir"];
            // Consider telling the dir that we are looking.
            $response["message"] = "Listing files in the specified directory.";
        } else {
            $dir = ".";
        }

        $response["files"] = $ftp->list_files($dir);
    }

    echo json_encode($response);