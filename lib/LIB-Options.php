<?php
class Options extends Core {
  // (A) CONSTRUCTOR - LOAD SYSTEM OPTIONS
  function __construct ($core) {
    parent::__construct($core);
    $options = $this->DB->fetchKV(
      "SELECT * FROM `options` WHERE `option_group`=?",
      [1], "option_name", "option_value"
    );
    foreach ($options as $k=>$v) { define($k, $v); }
  }

  // (B) GET OPTIONS
  //  $group : option group
  function getAll ($group=1) {
    return $this->DB->fetchAll(
      "SELECT * FROM `options` WHERE `option_group`=?", [$group]
    );
  }

  // (C) SAVE OPTIONS
  //  $options : array, key => value
  function save ($options) {
    foreach ($options as $k=>$v) {
      $this->DB->update("options",
        ["option_value"], "`option_name`=?", [$v, $k]
      );
    }
    return true;
  }
}
