<?php
$_PATH = explode("/", $_PATH);
if (count($_PATH)!=3) { exit("Invalid report"); }
$_CORE->autoCall("Report", $_PATH[1]);