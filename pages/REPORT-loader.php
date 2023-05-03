<?php
$req = explode("/", $_CORE->Route->path);
if (count($req)!=3) { exit("Invalid report"); }
$_CORE->autoCall("Report", $req[1]);