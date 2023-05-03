<?php
// CALLED BY $_CORE->ROUTES->API()
// USE THIS TO OVERRIDE API CORS PERMISSION
// $this->origin : client origin, e.g. http://site.com
// $this->orihost : client origin host, e.g. site.com
// $this->mod : requested module. e.g. users
// $this->act : requested action. e.g. save

/*
// (A) EXAMPLE - ALLOW "FOO.COM" TO ACCESS "TEST" MODULE
if ($this->orihost=="foo.com" && $this->mod=="test") {
  $access = true;
}

// (B) EXAMPLE - ALLOW "BAR.COM" TO ACCESS SOME ACTIONS IN "TEST" MODULE
$allowed = ["get", "getAll"];
if ($this->orihost=="bar.com" && $this->mod=="test" && in_array($this->act, $allowed)) {
  $access = true;
}
*/