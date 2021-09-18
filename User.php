<?php
    class User {
        private $user;
        private $pass;
        private $host;
        private $port;

        private $dir = ".";

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
            
            // The conn_id cannot be stored because it is resource. We have
            // to login again if we want to use it.
            $conn_id = ftp_connect($this->host, $this->port);

            if ($conn_id === FALSE) {
                return FALSE;
            }

            if (@ftp_login($conn_id, $this->user, $this->pass)) {
                $this->dir = ftp_pwd($conn_id);
                return TRUE;
            }

            return FALSE;
        }

        public function list_files() {

            $conn_id = ftp_connect($this->host, $this->port);
            ftp_login($conn_id, $this->user, $this->pass);

            // TODO: escape the folder name (for example spaces might be parsed as arguments in the command)

            // The -F argument adds suffic / to folders and @ to symlinks. No suffix means it is regular file.
            $files = ftp_nlist($conn_id, "-F " . $this->dir);

            $data = array();

            foreach ($files as $file) {

                switch (substr($file, -1)) {
                    case '/':
                        $type = "folder";
                        break;

                    case '@':
                        $type = "symlink";
                        break;

                    case '*':
                        $type = "executable";
                        break;

                    case '>':
                        $type = "door";
                        break;

                    case '=':
                        $type = "socket";
                        break;

                    case '|':
                        $type = "FIFO";
                        break;

                    default:
                        $type = "file";
                }

                array_push($data, array(
                    'name' => $file, 
                    'type' => $type
                ));
            }

            return $data;
        }

        public function change_dir($dir) {
            $conn_id = ftp_connect($this->host, $this->port);
            ftp_login($conn_id, $this->user, $this->pass);

            $this->dir = $dir;

            if (ftp_chdir($conn_id, $this->dir)) {
                return TRUE;
            } else { 
                return FALSE;
            }
        }

        public function current_dir() {
            return $this->dir;
        }
    }