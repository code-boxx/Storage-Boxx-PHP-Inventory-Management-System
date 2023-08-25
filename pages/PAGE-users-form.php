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

    <!-- FIXED TO "ADMIN" - ENABLE THIS SECTION IF YOU WANT USER ROLES
    <div class="form-floating mb-4">
      <select class="form-select" id="user_level" required><?php
        foreach (USR_LVL as $k=>$v) {
          printf("<option %svalue='%s'>%s</option>",
            $edit && $user["user_level"]==$k ? "selected " : "" ,
            $k, $v
          );
        }
      ?></select>
      <label>User Level</label>
    </div>
    -->
    <input type="hidden" id="user_level" value="A">

    <div class="form-floating mb-1">
      <input type="password" class="form-control" id="user_password" required>
      <label>Password</label>
    </div>
    <div class="text-secondary">* At least 8 alphanumeric characters.</div>
  </div>

  <button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(1)">
    <i class="ico-sm icon-undo2"></i> Back
  </button>
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-checkmark"></i> Save
  </button>
</form>