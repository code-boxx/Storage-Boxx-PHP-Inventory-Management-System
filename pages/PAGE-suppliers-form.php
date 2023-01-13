<?php
// (A) GET SUPPLIER
$edit = isset($_POST["id"]) && $_POST["id"]!="";
if ($edit) { $sup = $_CORE->autoCall("Suppliers", "get"); }

// (B) SUPPLIER FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> SUPPLIER</h3>
<form onsubmit="return sup.save()">
  <div class="bg-white border p-4 mb-3">
    <input type="hidden" id="sup-id" value="<?=isset($sup)?$sup["sup_id"]:""?>">

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">corporate_fare</span>
      </div>
      <input type="text" id="sup-name" class="form-control" required value="<?=$edit?$sup["sup_name"]:""?>" placeholder="Supplier Name">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">call</span>
      </div>
      <input type="text" id="sup-tel" class="form-control" required value="<?=$edit?$sup["sup_tel"]:""?>" placeholder="Supplier Telephone">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">email</span>
      </div>
      <input type="email" id="sup-email" class="form-control" required value="<?=$edit?$sup["sup_email"]:""?>" placeholder="Supplier Email">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">signpost</span>
      </div>
      <input type="text" id="sup-address" class="form-control" value="<?=$edit?$sup["sup_address"]:""?>" placeholder="Supplier Address (if any)">
    </div>
  </div>

  <input type="button" class="col btn btn-danger" value="Back" onclick="cb.page(0)">
  <input type="submit" class="col btn btn-primary" value="Save">
</form>