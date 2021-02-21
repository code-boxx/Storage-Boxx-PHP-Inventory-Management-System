<?php
class User {
  // (A) COUNTALL () : COUNT TOTAL NUMBER OF USERS
  //  $search : optional search term
  function countAll ($search="") {
    $sql = "SELECT COUNT(*) `c` FROM `users`";
    $cond = null;
    if ($search!="" && $search!=null) {
      $sql .= " WHERE `user_name` LIKE ? OR `user_email` LIKE ?";
      $cond = ["%$search%", "%$search%"];
    }
    $c = $this->core->fetch($sql, $cond);
    return $c['c'];
  }

  // (B) GETALL() : GET ALL USERS
  //  $search : optional search term
  //  $limit : optional limit SQL (for pagination)
  function getAll ($search="", $limit=true) {
    $sql = "SELECT * FROM `users`";
    $cond = null;
    if ($search!="" && $search!=null) {
      $sql .= " WHERE `user_name` LIKE ? OR `user_email` LIKE ?";
      $cond = ["%$search%", "%$search%"];
    }
    if ($limit) { $sql .= $this->core->Page->limit(); }
    return $this->core->fetchAll($sql, $cond, "user_id");
  }
  
  // (C) GET () : GET USER BY ID OR EMAIL
  // $id : user ID or email
  function get ($id) {
    return $this->core->fetch(
      sprintf("SELECT * FROM `users` WHERE `user_%s`=?", is_numeric($id)?"id":"email"),
      [$id]
    );
  }
  
  // (D) VERIFY () : VERIFY GIVEN EMAIL AND PASSWORD
  //  $email : user email
  //  $pass : user password
  //  $session : register user into session?
  function verify ($email, $pass, $session=true) {
    // (D1) GET USER
    $user = $this->get($email);
    if ($user==false) { 
      $this->error = "Invalid user/password";
      return false;
    }

    // (D2) VERIFY PASSWORD
    if (password_verify($pass, $user['user_password'])) {
      if ($session) { $_SESSION['user'] = [
        "id" => $user['user_id'],
        "name" => $user['user_name'],
        "email" => $user['user_email']
      ]; }
      return true;
    } else {
      $this->error = "Invalid user/password";
      return false;
    }
  }
  
  // (E) SAVE () : ADD/UPDATE USER
  //  $name : user name
  //  $email : user email
  //  $pass : user password (optional for edit)
  //  $id : user ID, for edit only
  function save ($name, $email, $pass="", $id=null) {
    // (E1) CHECK
    $check = $this->get($email);
    if ($id===null && is_array($check)) {
      $this->error = "$email is already registered";
      return false;
    }
    if ($id!==null && is_array($check) && $check['user_id']!=$id) {
      $this->error = "$email is already registered";
      return false;
    }

    // (E2) NEW USER
    if ($id===null) {
      $sql = "INSERT INTO `users` (`user_name`, `user_email`, `user_password`) VALUES (?,?,?)";
      $cond = [$name, $email, password_hash($pass, PASSWORD_DEFAULT)];
    }

    // (E3) UPDATE USER
    else {
      $sql = "UPDATE `users` SET `user_name`=?, `user_email`=?";
      $cond = [$name, $email];
      if ($pass!="" && $pass!=null) {
        $sql .= ", `user_password`=?";
        $cond[] = password_hash($pass, PASSWORD_DEFAULT);
      }
      $sql .= " WHERE `user_id`=?";
      $cond[] = $id;
    }

    // (E4) GO!
    return $this->core->exec($sql, $cond);
  }

  // (F) DEL () : DELETE USER
  //  $id : user ID
  function del ($id) {
    return $this->core->exec(
      "DELETE FROM `users` WHERE `user_id`=?", [$id]
    );
  }
}