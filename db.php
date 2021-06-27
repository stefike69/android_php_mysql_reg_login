<?php
  class DB {
    
    private $conn = NULL;
    private $ECH0 = TRUE;
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
      return $this->conn;
    }

    function select($comm) {
      try {
        $result = $this->getConn()->query($comm);
      } catch(Exception $e) {
        echo $e->errorMessage() . $this->E;
      }
      return $result;
    }

    function getConn() {
      if($this->conn == null) connect();
      return $this->conn;
    }

    function giveUserRow($userName) {
      $sql = "SELECT * FROM Users WHERE userName='".$userName."';";
      return $this->select($sql);
    }

    function giveMailRow($email) {
      $sql = "SELECT * FROM Users WHERE email='".$email."';";
      return $this->select($sql);
    }

    function isUserExists($userName, $email) {
      $sql = "SELECT * FROM Users WHERE userName='$userName' OR email='$email';";
      return (mysqli_num_rows($this->db->select($sql)) > 0);
    }

    function insertUser($rec) {
      $fields = "";
      $values = "";
      foreach($rec as $key => $value) {
        if (empty($fields)) {
          $fields = "(" . $key;
          $values = "('" . $value . "'";
        } else {
          $fields = ", " . $key;
          $values = ", '" . $value . "'";
        }
      }
      $fields .= ")";
      $values .= ")";
      $sql = "INSERT INTO Users ($fields) VALUES ($values);";
      $res =  $this->select($sql);
      return NULL;
    }

    function userNameToID($userName) {
      $sql = "SELECT ID FROM Users WHERE UserName='$userName';";
      $res = $this->select($sql);
      $row = convResToAssArray($res);
      return $row['ID'];
    }

    function dbClose() {
      try {
        mysqli_close($this->conn);
      } catch(Exception $e) {
        echo $e->errorMessage() . $this->E;
      }
    }

    function convResToAssArray($res) {
      return mysqli_fetch_assoc($res, MYSQLI_ASSOC);
    }

    function randomChars($length = 255){
      $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
                '0123456789`-=~!@#$%^&()_+,.;[]{}';
      $str = '';
      $max = strlen($chars) - 1;
      for ($i=0; $i < $length; $i++)
        $str .= $chars[random_int(0, $max)];
      return $str;
    }
    
    public function json_create($m, $e, $f) {
      $response = array();
      array_push($response, "MESSAGE" => $m);
      array_push($response, "ERROR"   => $e);
      array_push($response, "FIELDS"  => $f);
      return json_encode($response);
    }
    
    public function logging($dataPack) {
      if ($this->ECH0)
        alert($dataPack);
    }

    public function postToAssoc($post) {
      if (empty($post)) {
        $assoc = NULL;
      } else {
        $assoc = array();
        foreach($post as $key => $value) {
          $assoc[$key] = $value;
        }
      }
      return $assoc;
    }

  }
?>