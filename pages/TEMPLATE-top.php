<!DOCTYPE html>
<html>
  <head>
    <!-- (A) HTML HEAD & BACKGROUND LOADING STUFF -->
    <!-- (A1) META -->
    <title>Storage Boxx</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="robots" content="noindex, nofollow, nosnippet">
    <link rel="shortcut icon" href="<?=HOST_ASSETS?>favicon.png">

    <!-- (A2) BOOTSTRAP 5 -->
    <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="<?=HOST_ASSETS?>bootstrap.min.css"/>
    <script defer src="<?=HOST_ASSETS?>bootstrap.bundle.min.js"></script>

    <!-- (A3) GOOGLE MATERIAL ICONS -->
    <!-- https://fonts.google.com/icons -->
    <style>
    @font-face{font-family:"Material Icons";font-style:normal;font-weight:400;src:url(<?=HOST_ASSETS?>maticon.woff2) format("woff2");}
    .mi{font-family:"Material Icons";font-weight:400;font-style:normal;font-size:24px;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;word-wrap:normal;direction:ltr;-webkit-font-feature-settings:"liga";-webkit-font-smoothing:antialiased}
    .mi-big{font-size:32px}
    </style>

    <!-- (A4) STORAGE BOXX CLIENT ENGINE -->
    <link rel="stylesheet" href="<?=HOST_ASSETS?>storage-boxx.css"/>
    <script>var sbhost={base:"<?=HOST_BASE?>",assets:"<?=HOST_ASSETS?>",api:"<?=HOST_API_BASE?>"};</script>
    <script async src="<?=HOST_ASSETS?>storage-boxx.js"></script>
  </head>
  <body>
    <!-- (B) COMMON SHARED INTERFACE -->
    <!-- (B1) NOW LOADING -->
    <div id="sb-loading" class="d-flex justify-content-center align-items-center sb-hide">
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- (B2) TOAST MESSAGE -->
    <div class="position-fixed bottom-0 end-0 p-3">
      <div id="sb-toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <span id="sb-toast-icon" class="mi"></span>
          <strong id="sb-toast-head" class="me-auto p-1"></strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div id="sb-toast-body" class="toast-body"></div>
      </div>
    </div>

    <!-- (B3) MODAL DIALOG BOX -->
    <div id="sb-modal" class="modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 id="sb-modal-head" class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="sb-modal-body" class="modal-body"></div>
      <div id="sb-modal-foot" class="modal-footer">
      </div>
    </div></div></div>

    <?php if ($_USER !== false) { ?>
    <!-- (C) MAIN NAV BAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container-fluid">
      <!-- (C1) BRANDING LOGO
      <a class="navbar-brand" href="<?=HOST_BASE?>">
        <img src="<?=HOST_ASSETS?>favicon.png" loading="lazy" width="32" height="32"/>
      </a>
      -->

      <!-- (C2) MENU TOGGLE BUTTON -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="mi">menu</span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- (C3) LEFT MENU ITEMS -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>">
              <span class="mi mi-big">local_shipping</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>inventory">
              <span class="mi mi-big">inventory_2</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>users">
              <span class="mi mi-big">people</span>
            </a>
          </li>
        </ul>

        <!-- (C4) RIGHT LOGOUT -->
        <button class="btn btn-danger btn-sm" onclick="sb.bye()">
          <span class="mi">logout</span>
        </button>
      </div>
    </div></nav>
    <?php } ?>

    <!-- (D) PAGES -->
    <div class="container pt-4">
      <div id="sb-page-1" class="sb-page">
