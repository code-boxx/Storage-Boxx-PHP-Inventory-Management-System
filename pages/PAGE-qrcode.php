<div id="qrcode"></div>
<script src="<?=HOST_ASSETS?>qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("qrcode"), "<?=$_GET["sku"]?>");
</script>
