<?php
class MInstall extends Core {
  // (A) IMPORT SQL FILE
  function sql ($module) {
    $file = PATH_LIB . "SQL-$module.sql";
    if (!file_exists($file)) { exit("$file not found!"); }
    try {
      $this->DB->query(file_get_contents($file));
    } catch (Exception $ex) {
      exit("Unable to import $file - " . $ex->getMessage());
    }
  }

  // (B) BACKUP FILE
  function backup ($file) {
    if (!file_exists($file)) { exit("$file not found!"); }
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $bak = $ext == "htaccess" ? "$file.old" : str_replace(".$ext", ".old", $file) ;
    if (!copy($file, $bak)) { exit("Failed to backup $file"); }
  }

  // (C) APPEND TO FILE
  function append ($file, $add) {
    $this->backup($file);
    $fh = fopen($file, "a") or exit("Cannot open $file");
    if (fwrite($fh, $add) === false) {
      fclose($fh);
      exit("Failed to write to $file");
    }
    fclose($fh);
  }

  // (D) INSERT INTO FILE
  function insert ($file, $search, $add, $offset=0) {
    // (D1) BACKUP SPECIFIED FILE
    $this->backup($file);

    // (D2) SEEK "LINE TO INSERT AT"
    $lines = file($file);
    $at = -1;
    foreach ($lines as $l=>$line) {
      if (strpos($line, $search) !== false) { $at = $l + 1 + $offset; break; }
    }
    if ($at == -1) { exit("Failed to update $file"); }

    // (D3) INSERT INTO FILE
    array_splice($lines, $at, 0, $add);
    if (file_put_contents($file, implode("", $lines)) == false) {
      exit("Failed to update $file");
    }
  }

  // (E) CONDITIONAL INSERT
  function cinsert ($condition, $file, $search, $add, $offset=0) {
    $insert = true;
    $stream = fopen($file, "r");
    while($line = fgets($stream)) {
      if (strpos($line, $condition) !== false) { $insert = false; break; }
    }
    if ($insert) { $this->insert($file, $search, $add, $offset); }
  }

  // (F) CLEAN UP
  function clean ($module) {
    $file = PATH_PAGES . "PAGE-install-$module.php";
    if (!unlink($file)) { echo "Failed to delete $file, please do so manually."; }
    echo "Installation complete";
  }
}