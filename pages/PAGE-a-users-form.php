<?php
// (A) GET USER
$edit = isset($_POST["id"]) && $_POST["id"]!="";
if ($edit) {
  $user = $_CORE->autoCall("Users", "get");
}

// (B) USER FORM ?>
<form class="col-md-6 offset-md-3 bg-light border p-4" onsubmit="return usr.save()">
  <h4 class="mb-4"><?=$edit?"EDIT":"ADD"?> USER</h4>
  <input type="hidden" id="user_id" value="<?=isset($user)?$user["user_id"]:""?>"/>

  <div class="mb-4">
    <label for="user_name" class="form-label">Name</label>
    <input type="text" class="form-control" id="user_name" required value="<?=isset($user)?$user["user_name"]:""?>"/>
  </div>

  <div class="mb-4">
    <label for="user_email" class="form-label">Email</label>
    <input type="email" class="form-control" id="user_email" required value="<?=isset($user)?$user["user_email"]:""?>"/>
  </div>

  <div class="mb-4">
    <label for="user_pass" class="form-label">Password</label>
    <input type="password" class="form-control" id="user_password" required/>
  </div>

  <input type="button" class="col btn btn-danger btn-lg" value="Back" onclick="sb.page(1)"/>
  <input type="submit" class="col btn btn-primary btn-lg" value="Save"/>
</form>
