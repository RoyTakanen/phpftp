<?php
    class User {
        private $user;
        private $pass;
        private $host;
        private $port;

        private $ftp_conn;

        function __construct($user, $pass, $host, $port=21) {
            $this->user = $user;
            $this->pass = $pass;
            $this->host = $host;
            $this->port = $port;
        }

        public function login() {
            if (!filter_var(gethostbyname($this->host), FILTER_VALIDATE_IP)) { 
                return FALSE;
            }

            $this->ftp_conn = ftp_connect($this->host, $this->port);

            if ($this->ftp_conn === FALSE) {
                return FALSE;
            }

            ftp_login($this->ftp_conn, $this->user, $this->pass);
            return TRUE;
        }
    }