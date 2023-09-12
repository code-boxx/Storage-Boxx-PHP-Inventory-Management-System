<?php
class Forgot extends Core {
  // (A) SETTINGS
  private $valid = 900; // request valid for 15 minutes
  private $plen = 10; // random password will be 10 characters
  private $hlen = 24; // hash will be 24 characters

  // (B) PASSWORD RESET REQUEST
  function request ($email) {
    // (B1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (B2) CHECK IF VALID USER
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
    if ($user["user_level"] == "S") {
      $this->error = "$email is not an active account.";
      return false;
    }

    // (B3) CHECK PREVIOUS REQUEST (PREVENT SPAM)
    $req = $this->Users->hashGet($user["user_id"], "P");
    if (is_array($req)) {
      $expire = strtotime($req["hash_time"]) + $this->valid;
      $now = strtotime("now");
      $left = $now - $expire;
      if ($left <0) {
        $this->error = "Please wait another ".abs($left)." seconds.";
        return false;
      }
    }

    // (B4) CHECKS OK - CREATE NEW RESET REQUEST
    $now = strtotime("now");
    $hash = $this->Core->random($this->hlen);
    $this->Users->hashAdd($user["user_id"], "P", $hash);

    // (B5) SEND EMAIL TO USER
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

  // (C) PROCESS PASSWORD RESET
  function reset ($id, $hash) {
    // (C1) ALREADY SIGNED IN
    if (isset($_SESSION["user"])) {
      $this->error = "You are already signed in.";
      return false;
    }

    // (C2) CHECK REQUEST
    $this->Core->load("Users");
    $req = $this->Users->hashGet($id, "P");
    $pass = is_array($req);
    
    // (C3) CHECK EXPIRE
    if ($pass) {
      $expire = strtotime($req["hash_time"]) + $this->valid;
      $now = strtotime("now");
      $pass = $now <= $expire;
    }

    // (C4) CHECK HASH
    if ($pass) { $pass = $hash==$req["hash_code"]; }

    // (C5) GET USER
    if ($pass) {
      $user = $this->Users->get($id);
      $pass = is_array($user);
    }

    // (C6) CHECK FAIL - INVALID REQUEST
    if (!$pass) {
      $this->error = "Invalid request.";
      return false;
    }

    // (C7) CHECK PASS - PROCEED RESET
    // (C7-1) UPDATE USER PASSWORD
    $this->DB->start();
    $password = $this->Core->random($this->plen);
    $this->DB->update(
      "users", ["user_password"], "`user_id`=?",
      [password_hash($password, PASSWORD_DEFAULT), $id]
    );

    // (C7-2) REMOVE REQUEST
    $this->Users->hashDel($id, "P");

    // (C7-3) EMAIL TO USER
    $this->Core->load("Mail");
    $pass = $this->Mail->send([
      "to" => $user["user_email"],
      "subject" => "Password Reset",
      "template" => PATH_PAGES . "MAIL-forgot-b.php",
      "vars" => [
        "password" => $password
      ]
    ]);

    // (C8) CLOSE
    $this->DB->end($pass);
    return true;
  }
}