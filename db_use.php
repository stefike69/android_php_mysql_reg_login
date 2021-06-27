<?php
  require_once("db.php");

  class DB_Use {

    private $db = new DB(); // DON'T OPEN

    /**
     * felhasználó beléptetése ...
     */
    public function login($fields) {
      $un = $fields["userName"];
      $pw = $fields["password"];
      $dataPack = array();
      array_push($dataPack, array("FUNCTION", "login"));
      $error = "";
      if (empty($un) || empty($pw)) {
        $error = "error_e";
      } else {
        $this->db->connect();
        $res = $this->db->giveUserRow($un);
        $rec = $this->db->convResToAssArray($res);
        if ((mysqli_num_rows($res) == 0) || ($rec["password"] != $pw)) {
          $error = "error_w";
        }
        $this->db->dbClose();
      }
      array_push($dataPack, array("ERROR", $error));
      array_push($dataPack, array("userName", $un));
      array_push($dataPack, array("password", $pw));
      echo(json_encode($dataPack));
      $this->db->logging($datapack);
    }

    public function createUser() {
      $ln = $_POST["lastName"];
      $fn = $_POST["firstName"];
      $un = $_POST["userName"];
      $pw = $_POST["password"];
      $em = $_POST["email"];
      $ut = $_POST["userType"];
      $cm = $_POST["comment"];
      $error = "";
      if (empty($un) || empty($pw) || empty($em)) {
        $error = "error_e";
        if (empty($un)) $error.= "_un";
        if (empty($pw)) $error.= "_pw";
        if (empty($em)) $error.= "_em";
      } else {
        $fo = "";
        $this->db->connect();
        $sql = "SELECT * FROM Users WHERE userName='$un' OR email='$em';";
        $cou = mysqli_num_rows($this->db->select($sql));
        if ($cou > 0) {
          $error = "error_c";
          $sql = "SELECT * FROM Users WHERE userName='$un';";
          $cou = mysqli_num_rows($this->db->select($sql));
          $error .= (($cou > 0) ? "_un" : "");
          $sql = "SELECT * FROM Users WHERE email='$em';";
          $cou = mysqli_num_rows($this->db->select($sql));
          $error .= (($cou > 0) ? "_em" : "");
        } else {
          if ($ut == 2) {  // ut = TárTulajdonos
            do {
              $fo = $this->db->randomChars();
              $sql = "SELECT * FROM Users WHERE folder='$fo'";
              $res = $this->db->select($sql);
            } while (mysqli_num_rows($res) > 0);
          }
          $fields = "(lastName, firstName, userName, password, email, userType, folder, comment)";
          $values = "('$ln',    '$fn',     '$un',    '$pw',    '$em', $ut,      '$fo',  '$cm')";
          $sql = "INSERT INTO Users $fields VALUES $values;";
          $res =  $this->db->select($sql);
        }
        $this->db->dbClose();
        if ($ut == 2) mkdir($fo);
      }
      if (empty($error)) {
        echo("Felhasználó létrehozva.");
      } else {
        echo("Hiba: " . $error);
      }
      return (empty($error) ? "registered" : $error);
    }

    public function deleteUser() {
      $un = $_POST["userName"];
      $this->db->connect();
      $resID = $this->db->userNameToID($un);
      $sql1 = "DELETE FROM Prohibitions WHERE ID='$resID';";
      $sql2 = "DELETE FROM Users WHERE ID='$resID';";
      $res =  $this->db->select($sql1);
      $res =  $this->db->select($sql2);
      $this->db->dbClose();
      rmdir($fo);
    }

    public function inactivateUser() {
      $un = $_POST["userName"];
      $ut = $_POST["unlockTime"]; // "YYYY.MM.DD HH:mm:ss"
      $re = $_POST["reason"];
      $co = $_POST["comment"];
      $this->db->connect();
      $resID = $this->db->userNameToID($un);
      $fields = "UserID, UnlockTime, Reason, Comment";
      $values = "$resID, '$ut',      $re,    '$co'";
      $sql = "INSERT INTO Prohibitions $fields VALUES $values;";
      $res =  $this->db->select($sql);
      $this->db->dbClose();
    }

    public function deactivateUser() {
      $un = $_POST["userName"];
      $this->db->connect();
      $resID = $this->db->userNameToID($un);
      $sql = "DELETE FROM Prohibitions WHERE UserID=$resID;";
      $res = $this->db->select($sql);
      $this->db->dbClose();
    }

    public function getDB() {
      return $this->db;
    }
  }
?>