<!DOCTYPE html>
<html>
  <head>
    <!-- (A) HEAD -->
    <!-- (A1) TITLE, DESC, CHARSET, VIEWPORT -->
    <title><?=isset($_PMETA["title"])?$_PMETA["title"]:"Storage Boxx"?></title>
    <meta charset="utf-8">
    <meta name="description" content="<?=isset($_PMETA["desc"])?$_PMETA["desc"]:"Storage Boxx - Inventory Management System"?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
    <meta name="robots" content="noindex">

    <!-- (A2) WEB APP & ICONS -->
    <link rel="icon" href="<?=HOST_ASSETS?>favicon.png" type="image/png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="white">
    <link rel="apple-touch-icon" href="<?=HOST_ASSETS?>icon-512.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Storage Boxx">
    <meta name="msapplication-TileImage" content="<?=HOST_ASSETS?>icon-512.png">
    <meta name="msapplication-TileColor" content="#ffffff">

    <?php if (isset($_SESS["user"])) { ?>
    <!-- (A3) WEB APP MANIFEST -->
    <!-- https://web.dev/add-manifest/ -->
    <link rel="manifest" href="<?=HOST_BASE?>CB-manifest.json">

    <!-- (A4) SERVICE WORKER -->
    <script>if ("serviceWorker" in navigator) {
      navigator.serviceWorker.register("<?=HOST_BASE?>CB-worker.js", {scope: "./"});
    }</script>
    <?php } ?>

    <!-- (A5) LIBRARIES & SCRIPTS -->
    <!-- https://getbootstrap.com/ -->
    <!-- https://fonts.google.com/icons -->
    <link rel="stylesheet" href="<?=HOST_ASSETS?>bootstrap.min.css">
    <script defer src="<?=HOST_ASSETS?>bootstrap.bundle.min.js"></script>
    <style>
    @font-face{font-family:"Material Icons";font-style:normal;font-weight:400;src:url(<?=HOST_ASSETS?>maticon.woff2) format("woff2");}
    .mi{font-family:"Material Icons";font-weight:400;font-style:normal;font-size:24px;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;word-wrap:normal;direction:ltr;-webkit-font-feature-settings:"liga";-webkit-font-smoothing:antialiased}
    .mi-big{font-size:32px}.mi-smol{font-size:18px}
    #cb-loading{transition:opacity .3s}.cb-hide{opacity:0;visibility:hidden;height:0}.cb-pg-hide{display:none}
    #cb-loading{width:100vw;height:100vh;position:fixed;top:0;left:0;z-index:999;background:rgba(0,0,0,.7)}#cb-loading .spinner-border{width:80px;height:80px}
    .head{background:#ddd}.zebra .d-flex{background:#fff;margin-bottom:10px}.zebra .d-flex:nth-child(odd){background-color:#f1f1f1}.pagination{border:1px solid #d0e8ff;background:#f0f8ff}
    #cb-body,body{min-height:100vh}#cb-toggle{display:none}#cb-side{width:155px;flex-shrink:0}#cb-side a{color:#fff}#cb-side .mi{color:#6a6a6a}@media screen and (max-width:768px){#cb-toggle{display:block}#cb-side{display:none}#cb-side.show{display:block}}#reader{max-width:380px}
    </style>
    <script>var cbhost={base:"<?=HOST_BASE?>",api:"<?=HOST_API_BASE?>",assets:"<?=HOST_ASSETS?>"};</script>
    <script defer src="<?=HOST_ASSETS?>PAGE-cb.js"></script>

    <!-- (A6) ADDITIONAL SCRIPTS -->
    <?php if (isset($_PMETA["load"])) { foreach ($_PMETA["load"] as $load) {
      if ($load[0]=="s") {
        printf("<script src='%s'%s></script>", $load[1], isset($load[2]) ? " ".$load[2] : "");
      } else {
        printf("<link rel='stylesheet' href='%s'>", $load[1]);
      }
    }}
    if (isset($_PMETA)) { unset($_PMETA); } ?>
  </head>
  <body class="bg-light">
    <!-- (B) COMMON SHARED INTERFACE -->
    <!-- (B1) NOW LOADING -->
    <div id="cb-loading" class="d-flex justify-content-center align-items-center cb-hide">
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- (B2) TOAST MESSAGE -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:11">
      <div id="cb-toast" class="toast hide" role="alert">
        <div class="toast-header">
          <span id="cb-toast-icon" class="mi"></span>
          <strong id="cb-toast-head" class="me-auto p-1"></strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div id="cb-toast-body" class="toast-body"></div>
      </div>
    </div>

    <!-- (B3) MODAL DIALOG BOX -->
    <div id="cb-modal" class="modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <div class="modal-header">
        <h5 id="cb-modal-head" class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div id="cb-modal-body" class="modal-body"></div>
      <div id="cb-modal-foot" class="modal-footer">
      </div>
    </div></div></div>

    <!-- (C) MAIN INTERFACE -->
    <div id="cb-body" class="d-flex">
      <?php if (isset($_SESS["user"])) { ?>
      <!-- (C1) LEFT SIDEBAR -->
      <nav id="cb-side" class="bg-dark text-white p-2">
      <ul class="navbar-nav">
        <li class="nav-item">
          <img src="<?=HOST_ASSETS?>favicon.png" loading="lazy" width="32" height="32">
            <hr>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>">
              <span class="mi mi-smol">dashboard</span> Dashboard
            </a>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>reports">
              <span class="mi mi-smol">library_books</span> Reports
            </a>
            <hr>
          </li>
          <li class="nav-item">
            <div class="mb-2">Inventory</div>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>move">
              <span class="mi mi-smol">move_up</span> Movement
            </a>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>check">
              <span class="mi mi-smol">qr_code_scanner</span> Check Item
            </a>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>inventory">
              <span class="mi mi-smol">inventory</span> Items
            </a>
            <hr>
          </li>
          <li class="nav-item">
            <div class="my-2">Entities</div>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>users">
              <span class="mi mi-smol">people</span> Users
            </a>
            <hr>
          </li>
          <li class="nav-item">
            <div class="my-2">System</div>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>settings">
              <span class="mi mi-smol">settings</span> Settings
            </a>
            <a class="nav-link ms-2" href="<?=HOST_BASE?>about">
              <i class="mi mi-smol">info</i> About
            </a>
            <hr>
          </li>
        </ul>
      </nav>
      <?php } ?>

      <!-- (C2) RIGHT CONTENTS -->
      <div class="flex-grow-1">
        <?php if (isset($_SESS["user"])) { ?>
        <!-- (C2-1) TOP NAV -->
        <nav class="d-flex bg-dark text-white p-1">
          <div class="flex-grow-1">
            <button id="cb-toggle" class="btn btn-sm mi text-white" onclick="cb.toggle()">
              menu
            </button>
          </div>
          <button class="btn btn-sm text-white mi" onclick="cb.bye()">
            logout
          </button>
        </nav>
        <?php } ?>

        <!-- (C2-2) CONTENTS -->
        <div class="p-4">
          <div id="cb-page-1">