<?php
// (A) GET SUPPLIER
$edit = isset($_POST["id"]) && $_POST["id"]!="";
if ($edit) { $sup = $_CORE->autoCall("Suppliers", "get"); }

// (B) SUPPLIER FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> SUPPLIER</h3>
<form onsubmit="return sup.save()">
  <div class="bg-white border p-4 mb-3">
    <input type="hidden" id="sup-id" value="<?=isset($sup)?$sup["sup_id"]:""?>">

    <div class="form-floating mb-4">
      <input type="text" id="sup-name" class="form-control" required value="<?=$edit?$sup["sup_name"]:""?>">
      <label>Supplier Name</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" id="sup-tel" class="form-control" required value="<?=$edit?$sup["sup_tel"]:""?>">
      <label>Supplier Telephone</label>
    </div>

    <div class="form-floating mb-4">
      <input type="email" id="sup-email" class="form-control" required value="<?=$edit?$sup["sup_email"]:""?>">
      <label>Supplier Email</label>
    </div>

    <div class="form-floating">
      <input type="text" id="sup-address" class="form-control" value="<?=$edit?$sup["sup_address"]:""?>">
      <label>Supplier Address (If Any)</label>
    </div>
  </div>

  <input type="button" class="col btn btn-danger" value="Back" onclick="cb.page(1)">
  <input type="submit" class="col btn btn-primary" value="Save">
</form>