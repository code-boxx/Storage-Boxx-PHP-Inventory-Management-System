<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."html5-qrcode.min.js"],
  ["s", HOST_ASSETS."PAGE-move.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) STOCK MOVEMENT -->
<!-- (A1) HEADER -->
<nav class="navbar cb-grey mb-4">
<div class="container-fluid">
  <h4>Stock Movement</h4>
</div>
</nav>

<div class="container"><div class="row">
  <!-- (A2) MOVEMENT FORM -->
  <form class="col bg-light border p-4 m-1" autocomplete="off" onsubmit="return move.save()">
    <div class="mb-4">
      <label class="form-label" for="mvt-direction">Direction</label>
      <select class="form-control" id="mvt-direction">
        <option value="I">Stock In (Receive)</option>
        <option value="O">Stock Out (Release)</option>
        <option value="T">Stock Take (Audit)</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="form-label" for="mvt-qty">Quantity</label>
      <input type="number" class="form-control" id="mvt-qty" step="0.01" value="1.00" required/>
    </div>

    <div class="mb-4">
      <label class="form-label" for="mvt-notes">Notes (If Any)</label>
      <textarea class="form-control" id="mvt-notes"></textarea>
    </div>

    <div class="mb-4">
      <label class="form-label" for="mvt-sku">SKU (Enter or Scan)</label>
      <input type="text" class="form-control" id="mvt-sku" required autofocus/>
    </div>
    <div class="p-4" style="width:500px;margin:0 auto 1.5rem auto" id="reader"></div>

    <input type="submit" class="btn btn-primary" value="Save"/>
  </form>

  <!-- (A3) MOVEMENT HISTORY (FOR CURRENT SESSION) -->
  <div class="col bg-light border p-4 m-1">
    <ul class="list-group list-group-flush" id="mvt-result"></ul>
  </div>
</div></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
