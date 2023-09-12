<?php
// CALLED BY $_CORE->SESSION->__CONSTRUCT()
// USE THIS TO BUILD/OVERRIDE SESSION DATA WHEN UNPACKING THE JWT

// LOAD USER INFO INTO SESSION
if (isset($_SESSION["user"])) {
  $user = $this->DB->fetch(
    "SELECT * FROM `users` WHERE `user_id`=?", [$_SESSION["user"]["user_id"]]
  );
  if (!is_array($user) || (isset($user["user_level"]) && $user["user_level"]=="S")) {
    $this->destroy();
    throw new Exception("Invalid or expired session.");
  } else {
    unset($user["user_password"]);
    $_SESSION["user"] = $user;
  }
}