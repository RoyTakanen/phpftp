<?php
    session_start();
    header('Content-type: application/json');

    $logged_in = FALSE;
    if (!empty($_SESSION["user"])) {
        $logged_in = TRUE;
    }

    $response = array(
        'loggedIn' => $logged_in
    );

    echo json_encode($response);
