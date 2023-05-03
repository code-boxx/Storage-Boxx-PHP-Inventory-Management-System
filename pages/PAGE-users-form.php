<?php
// (A) GET USER
$edit = isset($_POST["id"]) && $_POST["id"]!="";
if ($edit) { $user = $_CORE->autoCall("Users", "get"); }

// (B) USER FORM ?>
<h3><?=$edit?"EDIT":"ADD"?> USER</h3>
<form onsubmit="return usr.save()">
  <div class="bg-white border p-4 my-3">
    <input type="hidden" id="user_id" value="<?=isset($user)?$user["user_id"]:""?>">
    <div class="form-floating mb-4">
      <input type="text" class="form-control" id="user_name" required value="<?=isset($user)?$user["user_name"]:""?>">
      <label>User Name</label>
    </div>

    <div class="form-floating mb-4">
      <input type="email" class="form-control" id="user_email" required value="<?=isset($user)?$user["user_email"]:""?>">
      <label>User Email</label>
    </div>

    <div class="form-floating">
      <input type="password" class="form-control" id="user_password" required>
      <label>Password, at least 8 characters alphanumeric.</label>
    </div>
  </div>

  <input type="button" class="col btn btn-danger" value="Back" onclick="cb.page(1)">
  <input type="submit" class="col btn btn-primary" value="Save">
</form>