<?php
    if (!isset($_GET["action"])) {
        header('Location: /');
    }

    $action = $_GET["action"];
    if (!isset($_GET["path"])) {
        $path = "/";
    } else {
        $path = $_GET["path"];
    }