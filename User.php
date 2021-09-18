<?php
    class User {
        private $user;
        private $pass;
        private $host;
        private $port;

        private $conn_id;

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

            $this->conn_id = ftp_connect($this->host, $this->port);

            if ($this->conn_id === FALSE) {
                return FALSE;
            }

            if (@ftp_login($this->conn_id, $this->user, $this->pass)) {
                return TRUE;
            }

            ftp_set_option($this->conn_id, FTP_TIMEOUT_SEC, 1800); // timeout 30 mins

            return FALSE;
        }

        public function list_files($dir=".") {
            return ftp_nlist($this->conn_id, $dir);
        }
    }