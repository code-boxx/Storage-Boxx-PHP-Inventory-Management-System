<?php require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) JAVASCRIPT -->
<script src="<?=URL_PUBLIC?>admin-inventory.js"></script>

<!-- (B) NAVIGATION -->
<h1>MANAGE INVENTORY</h1>
<form class="bar" onsubmit="return inv.search()">
  <input type="text" id="inv-search"/>
  <input type="submit" value="Search"/>
  <input type="button" value="Add" onclick="inv.addEdit()"/>
</form>

<!-- (C) INVENTORY LIST -->
<div id="inv-list"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>