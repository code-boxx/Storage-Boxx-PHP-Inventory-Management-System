<?php
class CCache extends Core {
  // (A) REGENERATE CLIENT STORAGE CACHE LIST
  function init () : void {
    // (A1) GET FILES TO STORE IN CACHE
    $all = [HOST_ASSETS . "favicon.png", HOST_ASSETS . "ico-512.png"];
    if (file_exists(PATH_ASSETS . "banner.webp")) {
      $all[] = HOST_ASSETS."banner.webp";
    }
    if (file_exists(PATH_ASSETS . "users.webp")) {
      $all[] = HOST_ASSETS."users.webp";
    }
    foreach (glob(PATH_ASSETS . "*.{js,css,map,woff}", GLOB_BRACE) as $f) {
      $all[] = HOST_ASSETS . basename($f);
    }
    file_put_contents(PATH_BASE . "CB-cache-files.json", json_encode($all));

    // (A2) UPDATE DATABASE TIMESTAMP
    $this->Core->load("DB");
    $this->DB->update("settings", ["setting_value"], "`setting_name`=?", [strtotime("now"), "CACHE_VER"]);
  }
}