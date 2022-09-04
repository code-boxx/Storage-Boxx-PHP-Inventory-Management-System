<?php
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-users.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) HEADER -->
<h3 class="mb-3">MANAGE USERS</h3>

<!-- (B) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return usr.search()">
  <input type="text" id="user-search" placeholder="Search" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary mi mx-1">
    search
  </button>
  <button class="btn btn-primary mi" onclick="usr.addEdit()">
    add
  </button>
</form>

<!-- (C) USERS LIST -->
<div id="user-list" class="zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>