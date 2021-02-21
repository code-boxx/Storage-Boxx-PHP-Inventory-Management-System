<?php
// (A) GET USER
$edit = isset($_POST['id']) && $_POST['id']!="";
if ($edit) {
  $_CORE->load("User");
  $user = $_CORE->User->get($_POST['id']);
  if (!is_array($user)) { exit("ERROR - USER NOT FOUND!"); }
}

// (B) USER FORM ?>
<h1><?=$edit?"EDIT":"ADD"?> USER</h1>
<form class="standard" onsubmit="return usr.save()">
  <input type="hidden" id="user_id" value="<?=isset($user)?$user['user_id']:""?>"/>
  <label for="user_name">Name</label>
  <input type="text" id="user_name" required value="<?=isset($user)?$user['user_name']:""?>"/>
  <label for="user_email">Email</label>
  <input type="email" id="user_email" required value="<?=isset($user)?$user['user_email']:""?>"/>
  <label for="user_pass">Password</label>
  <input type="password" id="user_password"<?=$edit?"":" required"?>/>
  <input type="submit" value="Save"/>
  <input type="button" value="Back" onclick="common.page('A')"/>
</form>