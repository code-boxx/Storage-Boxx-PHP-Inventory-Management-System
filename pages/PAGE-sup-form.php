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
      <textarea id="sup-address" class="form-control"><?=$edit?$sup["sup_address"]:""?></textarea>
      <label>Supplier Address (If Any)</label>
    </div>
  </div>

  <button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(1)">
    <i class="ico-sm icon-undo2"></i> Back
  </button>
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-checkmark"></i> Save
  </button>
</form>