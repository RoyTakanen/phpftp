<?php

    session_start();
    session_destroy();

    $response = array(
        'loggedin' => FALSE,
        'message' => "Succesfully logged out!"
    );

    echo json_encode($response);