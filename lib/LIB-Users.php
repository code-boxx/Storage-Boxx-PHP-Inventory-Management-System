<?php
class Users extends Core {
  // (A) PASSWORD CHECKER
  //  $password : password to check
  //  $pattern : regex pattern check (at least 8 characters, alphanumeric)
  function checker ($password, $pattern='/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/i') {
    if (preg_match($pattern, $password)) { return true; }
    else {
      $this->error = "Password must be at least 8 characters alphanumeric.";
      return false;
    }
  }

  // (B) ADD OR UPDATE USER
  //  $name : user name
  //  $email : user email
  //  $password : user password
  //  $id : user id (for updating only)
  function save ($name, $email, $password, $id=null) {
    // (B1) DATA SETUP + PASSWORD CHECK
    if (!$this->checker($password)) { return false; }
    $fields = ["user_name", "user_email", "user_password"];
    $data = [$name, $email, password_hash($password, PASSWORD_DEFAULT)];

    // (B2) ADD/UPDATE USER
    if ($id===null) {
      $this->DB->insert("users", $fields, $data);
    } else {
      $data[] = $id;
      $this->DB->update("users", $fields, "`user_id`=?", $data);
    }
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
      $this->Core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->Core->page["lim"];
    }

    // (E3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "user_id");
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
    $this->Session->create();
    return true;
  }

  // (H) LOGOUT
  function logout () {
    // (H1) ALREADY SIGNED OFF
    global $_SESS;
    if (!isset($_SESS["user"])) { return true; }

    // (H2) END SESSION
    $this->Session->destroy();
    return true;
  }

  // (I) CREATE NEW NFC TOKEN
  function token ($id) {
    // (I1) UPDATE TOKEN
    $token = $this->Core->random(4);
    $this->DB->update("users", ["user_token"], "`user_id`=?", [$token, $id]);

    // (I2) RETURN ENCODED TOKEN
    require PATH_LIB . "jwt/autoload.php";
    return Firebase\JWT\JWT::encode([$id, $token], JWT_SECRET, JWT_ALGO);
  }

  // (J) NULLIFY NFC TOKEN
  function notoken ($id) {
    $this->DB->update("users", ["user_token"], "`user_id`=?", [null, $id]);
    return true;
  }

  // (K) NFC TOKEN LOGIN
  function intoken ($token) {
    // (K1) DECODE TOKEN
    $valid = true;
    try {
      require PATH_LIB . "jwt/autoload.php";
      $token = Firebase\JWT\JWT::decode(
        $token, new Firebase\JWT\Key(JWT_SECRET, JWT_ALGO)
      );
      $valid = is_object($token);
      if ($valid) {
        $token = (array) $token;
        $valid = count($token)==2;
      }
    } catch (Exception $e) { $valid = false; }

    // (K2) VERIFY TOKEN
    if ($valid) {
      $user = $this->get($token[0]);
      $valid = (is_array($user) && $user["user_token"]==$token[1]);
    }

    // (K3) SESSION START
    if ($valid) {
      global $_SESS;
      $_SESS["user"] = $user;
      $this->Session->create();
      return true;
    }

    // (K4) NADA
    $this->error = "Invalid token";
    return false;
  }
}