<?php
// CALLED BY $_CORE->SESSION->__CONSTRUCT()
// USE THIS TO BUILD/OVERRIDE SESSION DATA WHEN UNPACKING THE JWT

// (A) LOAD USER INFO INTO SESSION
if (isset($_SESSION["user"])) {
  $user = $this->DB->fetch(
    "SELECT * FROM `users` WHERE `user_id`=?", [$_SESSION["user"]["user_id"]]
  );
  if (is_array($user)) {
    unset($user["user_password"]);
    $_SESSION["user"] = $user;
  } else {
    $this->destroy();
    throw new Exception("Invalid or expired session.");
  }
}