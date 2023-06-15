<?php
// (A-B) PROPERTIES, SETTINGS, HELPER
// (C-D) GET USERS
// (E-F) SAVE & DELETE USER
// (G-I) VERIFY, LOGIN, LOGOUT
// (J-L) NFC LOGIN TOKEN
class Users extends Core {
  // (A) SETTINGS
  private $nlen = 6; // 12 characters nfc login random hash

  // (B) PASSWORD CHECKER (HELPER)
  //  $password : password to check
  //  $pattern : regex pattern check (at least 8 characters, alphanumeric)
  function checker ($password, $pattern='/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/i') {
    if (preg_match($pattern, $password)) { return true; }
    else {
      $this->error = "Password must be at least 8 characters alphanumeric.";
      return false;
    }
  }

  // (C) GET USER
  //  $id : user id or email
  //  $hash : optional, also get validation hash
  function get ($id, $hash=null) {
    // (C1) SELECT
    $sql = sprintf(
      "SELECT %s FROM `users` u%s WHERE u.`user_%s`=?",
      $hash==null ? "u.*" : "u.*, h.`hash_code`, h.`hash_time`, h.`hash_tries`",
      $hash==null ? "" : " LEFT JOIN `users_hash` h ON (u.`user_id`=h.`user_id` AND h.`hash_for`=?)",
      is_numeric($id) ? "id" : "email"
    );
    $data = $hash==null ? [$id] : [$hash, $id];
    return $this->DB->fetch($sql, $data);
  }

  // (D) GET ALL OR SEARCH USERS (ADMIN USE)
  //  $search : optional, user name or email
  //  $page : optional, current page number
  function getAll ($search=null, $page=null) {
    // (D1) PARITAL USERS SQL + DATA
    $sql = "FROM `users`";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE `user_name` LIKE ? OR `user_email` LIKE ?";
      $data = ["%$search%", "%$search%"];
    }

    // (D2) PAGINATION
    if ($page != null) {
      $this->Core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->Core->page["lim"];
    }

    // (D3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "user_id");
  }

  // (E) ADD OR UPDATE USER (ADMIN/SECONDARY USE)
  //  $name : user name
  //  $email : user email
  //  $password : user password
  //  $lvl : user level - use this if you want to implement user roles
  //  $id : user id (for updating only)
  function save ($name, $email, $password, $lvl="A", $id=null) {
    // (E1) DATA SETUP + PASSWORD CHECK
    if (!$this->checker($password)) { return false; }
    $fields = ["user_name", "user_email", "user_password", "user_level"];
    $data = [$name, $email, password_hash($password, PASSWORD_DEFAULT), $lvl];

    // (E2) ADD/UPDATE USER
    if ($id===null) {
      $this->DB->insert("users", $fields, $data);
    } else {
      $data[] = $id;
      $this->DB->update("users", $fields, "`user_id`=?", $data);
    }
    return true;
  }

  // (F) DELETE USER (ADMIN USE)
  //  $id : user id
  function del ($id) {
    $this->DB->start();
    $this->DB->delete("users", "`user_id`=?", [$id]);
    $this->DB->delete("users_hash", "`user_id`=?", [$id]);
    $this->DB->end();
    return true;
  }

  // (G) VERIFY EMAIL & PASSWORD (LOGIN OR SECURITY CHECK)
  // RETURNS USER ARRAY IF VALID, FALSE IF INVALID
  //  $email : user email
  //  $password : user password
  function verify ($email, $password) {
    // (G1) GET USER
    $user = $this->get($email);
    if (!is_array($user)) {
      $this->error = "Invalid user or password.";
      return false;
    }

    // (G2) PASSWORD CHECK
    if (!password_verify($password, $user["user_password"])) {
      $this->error = "Invalid user or password.";
      return false;
    }
    return $user;
  }

  // (H) LOGIN
  //  $email : user email
  //  $password : user password
  function login ($email, $password) {
    // (H1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) { return true; }

    // (H2) VERIFY EMAIL PASSWORD ACCOUNT
    $user = $this->verify($email, $password);
    if ($user===false) { return false; }

    // (H3) SESSION START
    $_SESSION["user"] = $user;
    unset($_SESSION["user"]["user_password"]);
    unset($_SESSION["user"]["hash_code"]);
    unset($_SESSION["user"]["hash_time"]);
    $this->Session->save();
    return true;
  }

  // (I) LOGOUT
  function logout () {
    // (I1) ALREADY SIGNED OFF
    if (!isset($_SESSION["user"])) { return true; }

    // (I2) END SESSION
    $this->Session->destroy();
    return true;
  }
  
  // (J) CREATE NEW NFC LOGIN TOKEN
  //  $id : user id
  function token ($id) {
    // (J1) UPDATE TOKEN
    $token = $this->Core->random($this->nlen);
    $this->DB->replace("users_hash",
      ["user_id", "hash_for", "hash_code", "hash_time", "hash_tries"],
      [$id, "N", $token, date("Y-m-d H:i:s"), 0]
    );

    // (J2) RETURN ENCODED TOKEN
    require PATH_LIB . "JWT/autoload.php";
    return Firebase\JWT\JWT::encode([$id, $token], JWT_SECRET, JWT_ALGO);
  }

  // (K) NULLIFY NFC TOKEN
  function notoken ($id) {
    $this->DB->delete("users_hash", "`user_id`=? AND `hash_for`='N'", [$id]);
    return true;
  }

  // (L) NFC TOKEN LOGIN
  function intoken ($token) {
    // (L1) DECODE TOKEN
    $valid = true;
    try {
      require PATH_LIB . "JWT/autoload.php";
      $token = Firebase\JWT\JWT::decode(
        $token, new Firebase\JWT\Key(JWT_SECRET, JWT_ALGO)
      );
      $valid = is_object($token);
      if ($valid) {
        $token = (array) $token;
        $valid = count($token)==2;
      }
    } catch (Exception $e) { $valid = false; }

    // (L2) VERIFY TOKEN
    if ($valid) {
      $user = $this->get($token[0], "N");
      $valid = (is_array($user) && $user["hash_code"]==$token[1]);
    }

    // (L3) SESSION START
    if ($valid) {
      $_SESSION["user"] = $user;
      unset($_SESSION["user"]["user_password"]);
      unset($_SESSION["user"]["hash_code"]);
      unset($_SESSION["user"]["hash_time"]);
      unset($_SESSION["user"]["hash_tries"]);
      $this->Session->save();
      return true;
    }

    // (L4) NADA
    $this->error = "Invalid token";
    return false;
  }
}