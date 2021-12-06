<!-- (A) SCRIPTS -->
<script src="<?=HOST_ASSETS?>html5-qrcode.min.js"></script>
<script src="<?=HOST_ASSETS?>check.js"></script>

<!-- (B) CHECK ITEM -->
<nav class="navbar cb-grey mb-4">
<div class="container-fluid">
  <h4>CHECK ITEM</h4>
</div>
</nav>

<div class="row">
  <!-- (C) SCANNER -->
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
