<?php
// (A) GET USERS
$users = $_CORE->autoCall("Users", "getAll");

// (B) DRAW USERS LIST
if (is_array($users)) { foreach ($users as $id=>$u) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$u["user_name"]?></strong><br>
    <small><?=$u["user_email"]?></small>
  </div>
  <div>
    <button class="btn btn-danger btn-sm mi" onclick="usr.del(<?=$id?>)">
      delete
    </button>
    <button class="btn btn-primary btn-sm mi" onclick="usr.addEdit(<?=$id?>)">
      edit
    </button>
  </div>
</div>
<?php }} else { echo "No users found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("usr.goToPage");