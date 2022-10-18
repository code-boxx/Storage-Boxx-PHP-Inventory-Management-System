<?php
class Forgot extends Core {
  // (A) SETTINGS
  // request will be valid for n seconds.
  // also to prevent spam, cannot make another request until expire.
  private $valid = 900; // 15 mins = 900 secs

  // (B) GET PASSWORD RESET REQUEST
  function get ($id) {
    return $this->DB->fetch("SELECT * FROM `password_reset` WHERE `user_id`=?", [$id]);
  }

  // (C) PASSWORD RESET REQUEST
  function request ($email) {
    // (C1) ALREADY SIGNED IN
    global $_SESS;
    if (isset($_SESS["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (C2) CHECK IF VALID USER
    $this->core->load("Users");
    $user = $this->core->Users->get($email);
    if (!is_array($user)) {
      $this->error = "$email is not registered.";
      return false;
    }

    // (C3) CHECK PREVIOUS REQUEST (PREVENT SPAM)
    $req = $this->get($user["user_id"]);
    if (is_array($req)) {
      $expire = strtotime($req["reset_time"]) + $this->valid;
      $now = strtotime("now");
      $left = $now - $expire;
      if ($left <0) {
        $this->error = "Please wait another ".abs($left)." seconds.";
        return false;
      }
    }

    // (C4) CHECKS OK - CREATE NEW RESET REQUEST
    $now = strtotime("now");
    $hash = md5($user["user_email"] . $now); // random hash
    $this->DB->insert("password_reset",
      ["user_id", "reset_hash", "reset_time"],
      [$user["user_id"], $hash, date("Y-m-d H:i:s")], true
    );

    // (C5) SEND EMAIL TO USER
    $this->core->load("Mail");
    return $this->core->Mail->send([
      "to" => $user["user_email"],
      "subject" => "Password Reset",
      "template" => PATH_PAGES . "MAIL-forgot-a.php",
      "vars" => [
        "link" => HOST_BASE."forgot?i={$user["user_id"]}&h={$hash}"
      ]
    ]);
  }

  // (D) PROCESS PASSWORD RESET
  function reset ($id, $hash) {
    // (D1) ALREADY SIGNED IN
    global $_SESS;
    if (isset($_SESS["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (D2) CHECK REQUEST
    $req = $this->get($id);
    $pass = is_array($req);

    // (D3) CHECK EXPIRE
    if ($pass) {
      $expire = strtotime($req["reset_time"]) + $this->valid;
      $now = strtotime("now");
      $pass = $now <= $expire;
    }

    // (D4) CHECK HASH
    if ($pass) { $pass = $hash==$req["reset_hash"]; }

    // (D5) GET USER
    if ($pass) {
      $this->core->load("Users");
      $user = $this->core->Users->get($id);
      $pass = is_array($user);
    }

    // (D6) CHECK FAIL - INVALID REQUEST
    if (!$pass) {
      $this->error = "Invalid request.";
      return false;
    }

    // (D7) CHECK PASS - PROCEED RESET
    // (D7-1) UPDATE USER PASSWORD
    $this->DB->start();
    $password = $this->core->random(5);
    $this->DB->update(
      "users", ["user_password"], "`user_id`=?",
      [password_hash($password, PASSWORD_DEFAULT), $id]
    );

    // (D7-2) REMOVE REQUEST
    $this->DB->delete("password_reset", "`user_id`=?", [$id]);

    // (D7-3) EMAIL TO USER
    $this->core->load("Mail");
    $pass = $this->core->Mail->send([
      "to" => $user["user_email"],
      "subject" => "Password Reset",
      "template" => PATH_PAGES . "MAIL-forgot-b.php",
      "vars" => [
        "password" => $password
      ]
    ]);

    // (D8) CLOSE
    $this->DB->end($pass);
    return true;
  }
}