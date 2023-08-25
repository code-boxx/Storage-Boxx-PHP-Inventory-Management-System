<?php
// (A) GET BATCH
$edit = isset($_POST["sku"]) && $_POST["sku"]!="";
if ($edit) { $batch = $_CORE->autoCall("Move", "getB"); }

// (B) BATCH FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> BATCH</h3>
<form onsubmit="return batch.save()">
  <div class="bg-white border p-4 mb-3">
     <div class="form-floating mb-4">
      <input type="text" class="form-control" id="batch-sku" value="<?=$edit?$batch["item_sku"]:""?>" required<?=$edit?" disabled":""?>>
      <label>SKU</label>
    </div>

    <div class="form-floating mb-1">
      <input type="hidden" id="batch-oname" value="<?=$edit?$batch["batch_name"]:""?>">
      <input type="text" class="form-control" id="batch-name" required value="<?=$edit?$batch["batch_name"]:""?>">
      <label>Batch Name</label>
    </div>
    <span class="text-secondary" onclick="batch.nowName()">[Use current date/time]</span>

    <div class="form-floating mt-4">
      <input type="datetime-local" class="form-control" id="batch-expire" value="<?=$edit?$batch["batch_expire"]:""?>">
      <label>Expire Date (if any)</label>
    </div>

    <?php if (!$edit) { ?>
    <div class="form-floating mt-4">
      <input type="number" class="form-control" id="batch-qty" value="0" min="0" required>
      <label>Batch Quantity</label>
    </div>
    <?php } ?>
  </div>

  <button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(1)">
    <i class="ico-sm icon-undo2"></i> Back
  </button>
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-checkmark"></i> Save
  </button>
</form>