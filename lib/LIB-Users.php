<?php
// (A-B) PROPERTIES, SETTINGS, HELPER
// (C-D) GET USERS
// (E-H) SAVE & DELETE USER
// (I-K) VERIFY, LOGIN, LOGOUT
// (L-N) REGISTRATION, ACTIVATION
// (O-Q) USER HASH
// (R) IMPORT
class Users extends Core {
  // (A) SETTINGS
  private $hvalid = 900; // validation link good for 15 mins
  private $hlen = 12; // 12 characters validation hash

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
  //  $lvl : user level
  //  $id : user id (for updating only)
  function save ($name, $email, $password, $lvl, $id=null) {
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

  // (G) SUSPEND USER
  //  $id : user id
  function suspend ($id) {
    $this->DB->update("users",
      ["user_level"], "`user_id`=?",
      ["S", $id]
    );
  }

  // (H) UPDATE ACCOUNT (LIMITED "MY ACCOUNT" USER SAVE)
  //  $name : name
  //  $cpass : current password
  //  $pass : new password
  function update ($name, $cpass, $pass) {
    // (H1) MUST BE SIGNED IN
    if (!isset($_SESSION["user"])) {
      $this->error = "Please sign in first";
      return false;
    }

    // (H2) PASSWORD STRENGTH
    if (!$this->checker($pass)) { return false; }

    // (H3) VERIFY CURRENT PASSWORD
    if (!$this->verify($_SESSION["user"]["user_email"], $cpass)) {
      return false;
    }

    // (H4) UPDATE DATABASE
    $this->DB->update("users",
      ["user_name", "user_password"], "`user_id`=?",
      [$name, password_hash($pass, PASSWORD_DEFAULT), $_SESSION["user"]["user_id"]]
    );
    return true;
  }

  // (I) VERIFY EMAIL & PASSWORD (LOGIN OR SECURITY CHECK)
  // RETURNS USER ARRAY IF VALID, FALSE IF INVALID
  //  $email : user email
  //  $password : user password
  function verify ($email, $password) {
    // (I1) GET USER
    $user = $this->get($email, "A");
    if (!is_array($user)) {
      $this->error = "Invalid user or password.";
      return false;
    }

    // (I2) PENDING ACTIVATION
    if ($user["hash_code"]!=null) {
      $this->error = "Please activate your account first.";
      return false;
    }

    // (I3) SUSPENDED
    if ($user["user_level"]=="S") {
      $this->error = "Invalid user or password.";
      return false;
    }

    // (I4) PASSWORD CHECK
    if (!password_verify($password, $user["user_password"])) {
      $this->error = "Invalid user or password.";
      return false;
    }
    return $user;
  }

  // (J) LOGIN
  //  $email : user email
  //  $password : user password
  function login ($email, $password) {
    // (J1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) { return true; }

    // (J2) VERIFY EMAIL PASSWORD ACCOUNT
    $user = $this->verify($email, $password);
    if ($user===false) { return false; }

    // (J3) SESSION START
    $_SESSION["user"] = $user;
    unset($_SESSION["user"]["user_password"]);
    unset($_SESSION["user"]["hash_code"]);
    unset($_SESSION["user"]["hash_time"]);
    $this->Session->save();
    return true;
  }

  // (K) LOGOUT
  function logout () {
    // (K1) ALREADY SIGNED OFF
    if (!isset($_SESSION["user"])) { return true; }

    // (K2) END SESSION
    $this->Session->destroy();
    return true;
  }

  // (L) REGISTER USER (SIGN UP)
  //  $name : user name
  //  $email : user email
  //  $password : user password
  function register ($name, $email, $password) {
    // (L1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (L2) CHECK USER EXIST
    if (is_array($this->get($email))) {
      $this->error = "$email is already registered.";
      return false;
    }

    // (L3) CREATE ACCOUNT + SEND ACTIVATION LINK
    $this->DB->start();
    $ok = $this->save($name, $email, $password, "U");
    if ($ok) { $ok = $this->hsend($this->DB->lastID); }
    $this->DB->end($ok);
    return $ok;
  }

  // (M) GENERATE HASH & SEND ACTIVATION LINK
  //  $id : user id or email
  function hsend ($id) {
    // (M1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (M2) GET USER + HASH
    $user = $this->get($id, "A");
    if (!is_array($user)) {
      $this->error = "Invalid user";
      return false;
    }

    // (M3) HAS EXISTING HASH - CHECK EXPIRY
    if ($user["hash_code"]!=null) {
      $now = strtotime("now");
      $till = strtotime($user["hash_time"]) + $this->hvalid;
      if ($now < $till) {
        $this->error = "Please wait for another ".($till - $now)." seconds.";
        return false;
      }
    }

    // (M4) GENERATE RANDOM HASH
    $hash = $this->Core->random($this->hlen);
    $this->hashAdd($user["user_id"], "A", $hash);

    // (M5) SEND ACTIVATION LINK TO USER EMAIL
    $this->Core->load("Mail");
    return $this->Mail->send([
      "to" => $user["user_email"],
      "subject" => "Validate Your Email",
      "template" => PATH_PAGES . "MAIL-activate.php",
      "vars" => [
        "link" => HOST_BASE."activate?i={$user["user_id"]}&h={$hash}"
      ]
    ]);
  }

  // (N) ACTIVATE ACCOUNT
  //  $i : user id
  //  $h : hash code
  function hactivate ($i, $h) {
    // (N1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) {
      $this->error = "Already signed in";
      return false;
    }
    
    // (N2) GET USER + HASH
    $user = $this->get($i, "A");
    if (!is_array($user)) {
      $this->error = "Invalid user";
      return false;
    }
    if ($user["hash_time"]==null) {
      $this->error = "Account already active";
      return false;
    }

    // (N3) HASH CHECK
    if (strtotime("now") >= strtotime($user["hash_time"]) + $this->hvalid) {
      $this->error = "Activation link expired";
      return false;
    }
    if ($user["hash_code"]!=$h) {
      $this->error = "Invalid activation link";
      return false;
    }

    // (N4) ACTIVATE ACCOUNT
    $this->hashDel($i, "A");

    // (N5) LOGIN
    unset($user["user_password"]);
    unset($user["hash_code"]);
    unset($user["hash_time"]);
    $_SESSION["user"] = $user;
    $this->Session->save();
    return true;
  }

  // (O) HASH ADD
  //  $id : user id
  //  $for : hash for - "A"ctivation, "OTP", "P"assword reset, "GOO"gle, "NFC"
  //  $time : timestamp
  //    - null : use current time
  //    - string : specify your own
  //    - "" : don't change
  function hashAdd ($id, $for, $code, $time=null) : void {
    $fields = ["user_id", "hash_for", "hash_code"];
    $data = [$id, $for, $code];
    if ($time===null) { $fields[] = "hash_time"; $data[] = date("Y-m-d H:i:s"); }
    else if ($time!="") { $fields[] = "hash_time"; $data[] = $time; }
    $this->DB->replace("users_hash", $fields, $data);
  }

  // (P) HASH GET
  //  $id : user id
  //  $for : hash for
  function hashGet ($id, $for) {
    return $this->DB->fetch(
      "SELECT * FROM `users_hash` WHERE `user_id`=? AND `hash_for`=?",
      [$id, $for]
    );
  }

  // (Q) HASH DELETE
  //  $id : user id
  //  $for : hash for
  function hashDel ($id, $for) : void {
    $this->DB->delete(
      "users_hash", "`user_id`=? AND `hash_for`=?", [$id, $for]
    );
  }

  // (R) IMPORT USER
  //  $name : user name
  //  $email : user email
  //  $password : user password
  function import ($name, $email, $password) {
    // (R1) CHECK REGISTERED
    if (is_array($this->get($email))) {
      $this->error = "$email is already registered";
    }

    // (R2) SAVE
    return $this->save($name, $email, $password, "A");
  }
}