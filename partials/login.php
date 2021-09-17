<?php

    require_once 'User.php';

    $ftp = new User("a", "a", "a");

    if ($ftp->login() === FALSE) {
        echo "Login failed!";
    }
