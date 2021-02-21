<!DOCTYPE html>
<html>
  <head>
    <title>Storage Boxx</title>
    <meta name="robots" content="noindex, nofollow" />
  </head>
  <body>
    <svg id="code128"></svg>
    <script src="<?=URL_PUBLIC?>JsBarcode.code128.min.js"></script>
    <script>JsBarcode("#code128", "<?=$_GET['sku']?>");</script>
  </body>
</html>