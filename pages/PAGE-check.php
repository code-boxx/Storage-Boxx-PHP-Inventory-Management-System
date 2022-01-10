<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."html5-qrcode.min.js"],
  ["s", HOST_ASSETS."PAGE-check.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) CHECK ITEM -->
<nav class="navbar cb-grey mb-4">
<div class="container-fluid">
  <h4>CHECK ITEM</h4>
</div>
</nav>

<div class="row">
  <!-- (B) SCANNER -->
  <div class="p-4" style="width:500px;margin:0 auto 1.5rem auto" id="reader"></div>

  <!-- (D) MANUAL FORM -->
  <form class="bg-light border p-4" autocomplete="off" onsubmit="return check.verify()">
    <div class="mb-4">
      <label class="form-label" for="check-sku">SKU (Enter or Scan)</label>
      <input type="text" class="form-control" id="check-sku" required autofocus/>
    </div>
    <input type="submit" class="btn btn-primary" value="Check"/>
  </form>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
