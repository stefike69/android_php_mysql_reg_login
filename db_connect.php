<?php

  class DBConnect {
  
    private $conn = NULL;
    private $E = PHP_EOL;

    function __construct($doit = FALSE) {
      if ($doit && is_null($this->conn)) {
        $this->connect();
      }
    }

    function connect() {
      include_once dirname(__FILE__) . '/constants.php';
      $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      if (mysqli_connect_errno())
        echo("Can't connect to database!" . mysqli_connect_err());
    }

    function getConn() {
      if($this->conn == null) connect();
      return $this->conn;
    }

    function dbClose() {
      try {
        mysqli_close($this->conn);
      } catch(Exception $e) {
        echo $e->errorMessage() . $this->E;
      }
    }

  }
?>