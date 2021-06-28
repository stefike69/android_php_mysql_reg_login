<?php
  require_once("db_connect.php");

  class MainFunctions {

    private $db = new DBCónnect(); // DON'T OPEN

    /**
     * Felhasználó beléptetése
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
      $this->db->logging($dataPack);
    }

    /**
     * Új felhasználó törzsadatainak tárolása
     */
    public function createUser($fields) {
      $ln = $fields["lastName"];
      $fn = $fields["firstName"];
      $un = $fields["userName"];
      $pw = $fields["password"];
      $em = $fields["email"];
      $ut = $fields["userType"];
      $cm = $fields["comment"];
      $dataPack = array();
      array_push($dataPack,array("FUNCTION", "createUser"));
      $error = "";
      $id = 0;
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
      array_push($dataPack, array("ERROR", $error));
      array_push($dataPack, array("ID", $id));

      return (empty($error) ? "registered" : $error);
    }

    /**
     * Felhasználó törlése
     */
    public function deleteUser($fields) {
      $un = $fields["userName"];
      $this->db->connect();
      $resID = $this->db->userNameToID($un);
      $sql1 = "DELETE FROM Prohibitions WHERE ID='$resID';";
      $sql2 = "DELETE FROM Users WHERE ID='$resID';";
      $res =  $this->db->select($sql1);
      $res =  $this->db->select($sql2);
      $this->db->dbClose();
      rmdir($fo);
    }

    /**
     * Felhasználó karanténba helyezése
     */
    public function inactivateUser($fields) {
      $un = $fields["userName"];
      $ut = $fields["unlockTime"]; // "YYYY.MM.DD HH:mm:ss"
      $re = $fields["reason"];
      $co = $fields["comment"];
      $this->db->connect();
      $resID = $this->db->userNameToID($un);
      $fields = "UserID, UnlockTime, Reason, Comment";
      $values = "$resID, '$ut',      $re,    '$co'";
      $sql = "INSERT INTO Prohibitions $fields VALUES $values;";
      $res =  $this->db->select($sql);
      $this->db->dbClose();
    }

    /**
     * Felhasználói karantén törlése
     */
    public function deactivateUser($fields) {
      $un = $fields["userName"];
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