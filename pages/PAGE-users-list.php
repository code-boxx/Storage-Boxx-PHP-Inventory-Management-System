<?php
// (A) GET USERS
$users = $_CORE->autoCall("Users", "getAll");

// (B) DRAW USERS LIST
if (is_array($users["data"])) { foreach ($users["data"] as $id=>$u) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1" style="display: flex;">
    <img alt="event-thumbnail" src="./images/profileimg/<?=$u["user_profilepic"] ? $u["user_profilepic"] : 'default.png'?>" loading="lazy" width="64" height="64"/>
    <div style="padding-top: 8px;">
      <strong>Username: <?=$u["user_name"]?></strong><br>
      <small>Email: <?=$u["user_email"]?></small>
    </div>
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
<?php }} else { ?>
<div class="d-flex align-items-center border p-2">No users found.</div>
<?php }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw($users["page"], "usr.goToPage");
