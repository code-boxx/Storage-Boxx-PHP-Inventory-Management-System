<?php
class Settings extends Core {
  // (A) CONSTRUCTOR - LOAD SYSTEM SETTINGS
  function __construct ($core) {
    parent::__construct($core);
    $this->defineG(1);
  }

  // (B) AUTO DEFINE BY SETTING GROUP
  //  $group : setting group
  function defineG ($group) {
    foreach ($this->DB->fetchKV(
      "SELECT * FROM `settings` WHERE `setting_group`=?",
      [$group], "setting_name", "setting_value"
    ) as $k=>$v) { define($k, $v); }
  }

  // (C) AUTO DEFINE BY SETTING NAME
  //  $name : setting name (string or array)
  //  $json : json decode setting value?
  function defineN ($name, $json=false) {
    // (C1) SQL & DATA
    $sql = "SELECT * FROM `settings` WHERE `setting_name`";
    if (is_array($name)) {
      $sql .= " IN (";
      foreach ($name as $n) { $sql .= "?,"; }
      $sql = substr($sql,0,-1) . ")";
      $data = $name;
    } else {
      $sql .= "=?";
      $data = [$name];
    }

    // (C2) GET & DEFINE
    foreach ($this->DB->fetchKV(
      $sql, $data, "setting_name", "setting_value"
    ) as $k=>$v) { define($k, ($json?json_decode($v,true):$v)); }
  }

  // (D) GET SETTINGS
  //  $group : setting group
  function getAll ($group=1) {
    return $this->DB->fetchAll("SELECT * FROM `settings` WHERE `setting_group`=?", [$group]);
  }

  // (E) SAVE SETTINGS
  //  $settings : array, key => value
  function save ($settings) {
    foreach ($settings as $k=>$v) {
      $this->DB->update("settings", ["setting_value"], "`setting_name`=?", [$v, $k]);
    }
    return true;
  }
}