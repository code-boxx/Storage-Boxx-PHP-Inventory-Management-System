<!-- (A) HEADER -->
<h3 class="mb-31">IMPORT <?=$_POST["name"]?></h3>

<!-- (B) SELECT CSV FILE -->
<?php $cols = json_decode($_POST["cols"], true); ?>
<div id="import-select" class="bg-white border p-4 mb-3">
  <div class="form-floating mb-4">
    <input type="file" class="form-control" required accept=".csv" id="import-file" onchange="im.read()">
    <label>Select CSV File</label>
  </div>

  <div class="form-floating mb-1">
    <textarea class="form-control" readonly><?php
      echo implode(" | ", $cols);
    ?></textarea>
    <label>CSV Columns</label>
  </div>
  <div class="text-secondary">
    * <a href="<?=HOST_ASSETS?><?=$_POST["eg"]?>">Download Example CSV</a>
  </div>
</div>

<!-- (C) IMPORT LIST -->
<table id="import-table" class="table table-striped d-none">
  <thead><tr class="table-dark">
    <?php foreach ($cols as $c) { echo "<th>$c</th>"; } ?>
    <th>Status</th>
  </tr></thead>
  <tbody id="import-list"></tbody>
<table>

<!-- (D) BACK & GO -->
<button type="button" id="import-back" onclick="cb.page(<?=$_POST["back"]?>)" class="my-1 me-1 btn btn-danger d-flex-inline">
  <i class="ico-sm icon-undo2"></i> Back
</button>
<button type="button" id="import-go" disabled onclick="im.go(true)" class="my-1 btn btn-primary d-flex-inline">
  <i class="ico-sm icon-cloud-upload"></i> Start
</button>