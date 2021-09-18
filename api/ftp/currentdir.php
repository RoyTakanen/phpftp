<?php
    require_once '../../User.php';

    session_start();
    header('Content-type: application/json');

    // TODO: proper response codes...

    $response = array(
        'error' => TRUE,
        'message' => "Unauthenticated",
        'loggedin' => FALSE
    );

    if (!empty($_SESSION["user"])) {
        $ftp = $_SESSION["user"];

        $current_dir = $ftp->current_dir();

        $response->error = FALSE;
        $response->message = "Checked current directory.";
        $response->current_dir = $current_dir;
    }
