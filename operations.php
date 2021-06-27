<?php
  require_once("db_use.php");
  if (isset($_POST)) {
    if (isset($_POST['FUNCTION'])) {
      $dbu = new DB_Use();
      $func = $_POST['FUNCTION'];
      $fields = $dbu->getDB()->postToAssoc($_POST);
      switch ($func) {
        case 'login':
          $dbu->login($fields);
          break;
        case 'createUser':
          $dbu->createUser($fields);
          break;
        case 'setUser':
          $dbu->setUser($fields);
          break;
        case 'setBank':
          $dbu->setBank($fields);
          break;
        case 'setLock':
          $dbu->setLock($fields);
          break;
        case 'setUnlock':
          $dbu->setUnlock($fields);
          break;
        case 'deleteUser':
          $dbu->deleteUser($fields);
          break;
        case 'bankBalance':
          $dbu->bankBalance($fields);
          break;
        default:
          break;
      }
    }
  }
?>