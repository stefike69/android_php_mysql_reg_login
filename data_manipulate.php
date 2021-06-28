<?php

  require_once('db_connect.php');

  class DataManipulate() {

    private $E = PHP_EOL;
    private $connect = new DBConnect();

    function select($comm) {
      try {
        $result = $this->getConn()->query($comm);
      } catch(Exception $e) {
        echo $e->errorMessage() . $this->E;
      }
      return $result;
    }

    function getRowByName($userName) {
      $un = $mysqli->real_escape_string($userName);
      $stmt = $mysqli->prepare("SELECT * FROM Users WHERE userName=?");
      $stmt->bind_param("s", $un);
      return doStmt($stmt);
    }

    function getRowByMail($email) {
      $em = $mysqli->real_escape_string($email);
      $stmt = $mysqli->prepare("SELECT * FROM Users WHERE email=?");
      $stmt->bind_param("s", $em);
      return doStmt($stmt);
    }

    function isUserExists($userName, $email) {
      $un = $mysqli->real_escape_string($userName);
      $em = $mysqli->real_escape_string($email);
      $stmt = $mysqli->prepare("SELECT COUNT(*) AS cnt FROM Users WHERE userName=? OR email=?");
      $stmt->bind_param("ss", $un, $em);
      $res = doStmt($stmt);
      return ($res['cnt'] > 0);
    }

    function userNameToID($userName) {
      $un = $mysqli->real_escape_string($userName);
      $stmt = mysqli->prepare("SELECT ID FROM Users WHERE userName=?");
      $stmt->bind_param("s", $un);
      $userRow = doStmt($stmt);
      return $userRow['ID'];
    }

    function doStmt(&$stmt) {
      $stmt->execute();
      $stmt->bind_result($resultRow); // !
      $stmt->fetch();
      $stmt->close();
      return $resultRow;
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