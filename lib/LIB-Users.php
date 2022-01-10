<?php
class Users extends Core {
  // (A) ADD OR UPDATE USER
  //  $name : user name
  //  $email : user email
  //  $password : user password
  //  $id : user id (for updating only)
  function save ($name, $email, $password, $id=null) {
    // (A1) DATA SETUP
    $fields = ["user_name", "user_email", "user_password"];
    $data = [$name, $email, password_hash($password, PASSWORD_DEFAULT)];

    // (A2) ADD/UPDATE USER
    if ($id===null) {
      $this->DB->insert("users", $fields, $data);
    } else {
      $data[] = $id;
      $this->DB->update("users", $fields, "`user_id`=?", $data);
    }
    return true;
  }

  // (B) REGISTER USER - RESTRICTED VERSION OF "SAVE" FOR FRONT-END
  //  $name : user name
  //  $email : user email
  //  $password : user password
  function register ($name, $email, $password) {
    // (B1) ALREADY SIGNED IN
    global $_SESS;
    if (isset($_SESS["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (B2) CHECK USER EXIST
    if (is_array($this->get($email))) {
      $this->error = "$email is already registered.";
      return false;
    }

    // (B3) ADD YOUR OWN CHECKS
    // PASSWORD LENGTH?
    // USER ROLE?

    // (B4) SAVE
    $this->save($name, $email, $password);
    return true;
  }

  // (C) DELETE USER
  //  $id : user id
  function del ($id) {
    $this->DB->delete("users", "`user_id`=?", [$id]);
    return true;
  }

  // (D) GET USER
  //  $id : user id or email
  function get ($id) {
    return $this->DB->fetch(
      "SELECT * FROM `users` WHERE `user_". (is_numeric($id)?"id":"email") ."`=?",
      [$id]
    );
  }

  // (E) GET ALL OR SEARCH USERS
  //  $search : optional, user name or email
  //  $page : optional, current page number
  function getAll ($search=null, $page=null) {
    // (E1) PARITAL USERS SQL + DATA
    $sql = "FROM `users`";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE `user_name` LIKE ? OR `user_email` LIKE ?";
      $data = ["%$search%", "%$search%"];
    }

    // (E2) PAGINATION
    if ($page != null) {
      $pgn = $this->core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= " LIMIT {$pgn["x"]}, {$pgn["y"]}";
    }

    // (E3) RESULTS
    $users = $this->DB->fetchAll("SELECT * $sql", $data, "user_id");
    return $page != null
     ? ["data" => $users, "page" => $pgn]
     : $users ;
  }

  // (F) VERIFY EMAIL & PASSWORD (LOGIN OR SECURITY CHECK)
  // RETURNS USER ARRAY IF VALID, FALSE IF INVALID
  //  $email : user email
  //  $password : user password
  function verify ($email, $password) {
    // (F1) GET USER
    $user = $this->get($email);
    $pass = is_array($user);

    // (F2) PASSWORD CHECK
    if ($pass) {
      $pass = password_verify($password, $user["user_password"]);
    }

    // (F3) RESULTS
    if (!$pass) {
      $this->error = "Invalid user or password.";
      return false;
    }
    return $user;
  }

  // (G) LOGIN
  //  $email : user email
  //  $password : user password
  function login ($email, $password) {
    // (G1) ALREADY SIGNED IN
    global $_SESS;
    if (isset($_SESS["user"])) { return true; }

    // (G2) VERIFY EMAIL PASSWORD
    $user = $this->verify($email, $password);
    if ($user===false) { return false; }

    // (G3) SESSION START
    $_SESS["user"] = $user;
    $this->core->Session->create();
    return true;
  }

  // (H) LOGOUT
  function logout () {
    // (H1) ALREADY SIGNED OFF
    global $_SESS;
    if (!isset($_SESS["user"])) { return true; }

    // (H2) END SESSION
    $this->core->Session->destroy();
    return true;
  }
}
