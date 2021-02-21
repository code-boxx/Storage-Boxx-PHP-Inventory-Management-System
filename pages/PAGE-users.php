<?php require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) JAVASCRIPT -->
<script src="<?=URL_PUBLIC?>admin-users.js"></script>

<!-- (B) NAVIGATION -->
<h1>MANAGE USERS</h1>
<form class="bar" onsubmit="return usr.search()">
  <input type="text" id="user-search"/>
  <input type="submit" value="Search"/>
  <input type="button" value="Add" onclick="usr.addEdit()"/>
</form>

<!-- (C) USERS LIST -->
<div id="user-list"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>