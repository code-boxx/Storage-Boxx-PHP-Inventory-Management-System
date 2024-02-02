<script>
window.onload = () => new QRCode("qrcode", {
  text: '<?=$qr?>',
  width: 300, height: 300,
  colorDark : "#000000",
  colorLight : "#ffffff",
  correctLevel : QRCode.CorrectLevel.H
});
</script>

<div id="qrwrap">
  <div id="qrcode"></div>
  <?php if ($for=="item") { ?>
  <div id="qrsku"><?=$item["item_sku"]?></div>
  <div id="qritem"><?=$item["item_name"]?></div>
  <div id="qrdesc"><?=$item["item_desc"]?></div>
  <?php } ?>
</div>