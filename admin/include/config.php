<?php
class dbConfig {
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;
    function dbConfig() {
        $this -> serverName = '127.0.0.1:4306';
        $this -> userName = 'root';
        $this -> password = '';
        $this -> dbName = 'test';
    }
}
?>