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
        $response["message"] = "Listed files in the current directory.";
        $response["loggedin"] = TRUE;

        $response["files"] = $ftp->list_files();
    }

    echo json_encode($response);