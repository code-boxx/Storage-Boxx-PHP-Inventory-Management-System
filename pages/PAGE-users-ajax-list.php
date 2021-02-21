<?php
// (A) GET USERS
$_CORE->load("Page");
$users = $_CORE->Page->autoGet("User", "countAll", "getAll");

// (B) DRAW USERS LIST
if (is_array($users)) { ?> 
<table class="zebra">
  <?php foreach ($users as $id=>$u) {?>
  <tr>
    <td>
      <strong><?=$u['user_name']?></strong><br>
      <small><?=$u['user_email']?></small>
    </td>
    <td class="right">
      <input type="button" value="Delete" onclick="usr.del(<?=$id?>)"/>
      <input type="button" value="Edit" onclick="usr.addEdit(<?=$id?>)"/>
    </td>
  </tr>
  <?php } ?>
</table>
<?php } else { echo "<div>No users found.</div>"; }

// (C) PAGINATION
$_CORE->Page->draw("usr.goToPage", "J");