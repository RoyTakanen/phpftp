<?php
    require_once '../User.php';

    session_start();
    header('Content-type: application/json');

    $_POST = json_decode(file_get_contents('php://input'), true);

    $response = array(
        'error' => TRUE,
        'message' => "Please provide host, port, user & pass in post request parameters.",
        'loggedin' => FALSE
    );

    if (isset($_POST["host"])  && isset($_POST["port"]) && isset($_POST["user"]) && isset($_POST["pass"])) {
        $host = $_POST["host"];
        $user = $_POST["user"];
        $pass = $_POST["pass"];
        $port = $_POST["port"];

        $ftp = new User($user, $pass, $host, $port);

        if ($ftp->login()) {
            $response["error"] = FALSE;
            $response["message"] = "Login success."; 
            $response["loggedin"] = TRUE;

            $_SESSION["user"] = $ftp;
        } else {
            $response["message"] = "Login failed.";
        }
    }

    echo json_encode($response);