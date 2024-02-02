<?php
// (A) GET USERS
$users = $_CORE->autoCall("Users", "getAll");

// (B) DRAW USERS LIST
if (is_array($users)) { foreach ($users as $id=>$u) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$u["user_name"]?></strong><br>
    <small>
      <?php /* <span class="badge bg-secondary">level</span> <?=USR_LVL[$u["user_level"]]?><br> */ ?>
      <span class="badge bg-secondary">email</span> <?=$u["user_email"]?>
    </small>
  </div>
  <div class="dropdown">
    <button type="button" class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="usr.addEdit(<?=$id?>)">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <li class="dropdown-item" onclick="usr.qrShow(<?=$id?>)">
        <i class="text-secondary ico-sm icon-qrcode"></i> QR Login
      </li>
      <li class="dropdown-item" onclick="usr.nfcShow(<?=$id?>)">
        <i class="text-secondary ico-sm icon-feed"></i> NFC Login
      </li>
      <li class="dropdown-item text-warning" onclick="usr.del(<?=$id?>)">
        <i class="ico-sm icon-bin2"></i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No users found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("usr.goToPage");