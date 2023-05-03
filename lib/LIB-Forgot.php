<?php
class Forgot extends Core {
  // (A) SETTINGS
  private $valid = 900; // request valid for 15 minutes
  private $plen = 5; // random password will be 10 characters
  private $hlen = 12; // hash will be 24 characters

  // (B) GET PASSWORD RESET REQUEST
  function get ($id) {
    return $this->DB->fetch(
      "SELECT * FROM `users_hash` WHERE `user_id`=? AND `hash_for`=?",
      [$id, "P"]
    );
  }

  // (C) PASSWORD RESET REQUEST
  function request ($email) {
    // (C1) ALREADY SIGNED IN
    if (isset($this->Session->data["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (C2) CHECK IF VALID USER
    $this->Core->load("Users");
    $user = $this->Users->get($email, "A");
    if (!is_array($user)) {
      $this->error = "$email is not registered.";
      return false;
    }
    if (isset($user["hash_code"])) {
      $this->error = "$email is not an active account.";
      return false;
    }

    // (C3) CHECK PREVIOUS REQUEST (PREVENT SPAM)
    $req = $this->get($user["user_id"]);
    if (is_array($req)) {
      $expire = strtotime($req["hash_time"]) + $this->valid;
      $now = strtotime("now");
      $left = $now - $expire;
      if ($left <0) {
        $this->error = "Please wait another ".abs($left)." seconds.";
        return false;
      }
    }

    // (C4) CHECKS OK - CREATE NEW RESET REQUEST
    $now = strtotime("now");
    $hash = $this->Core->random($this->hlen);
    $this->DB->insert("users_hash",
      ["user_id", "hash_for", "hash_code", "hash_time"],
      [$user["user_id"], "P", $hash, date("Y-m-d H:i:s")], true
    );

    // (C5) SEND EMAIL TO USER
    $this->Core->load("Mail");
    return $this->Mail->send([
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
    if (isset($this->Session->data["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (D2) CHECK REQUEST
    $req = $this->get($id);
    $pass = is_array($req);
    
    // (D3) CHECK EXPIRE
    if ($pass) {
      $expire = strtotime($req["hash_time"]) + $this->valid;
      $now = strtotime("now");
      $pass = $now <= $expire;
    }

    // (D4) CHECK HASH
    if ($pass) { $pass = $hash==$req["hash_code"]; }

    // (D5) GET USER
    if ($pass) {
      $this->Core->load("Users");
      $user = $this->Users->get($id);
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
    $password = $this->Core->random($this->plen);
    $this->DB->update(
      "users", ["user_password"], "`user_id`=?",
      [password_hash($password, PASSWORD_DEFAULT), $id]
    );

    // (D7-2) REMOVE REQUEST
    $this->DB->delete("users_hash", "`user_id`=? AND `hash_for`=?", [$id, "P"]);

    // (D7-3) EMAIL TO USER
    $this->Core->load("Mail");
    $pass = $this->Mail->send([
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