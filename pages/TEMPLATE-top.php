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

    <!-- (A3) BURN-IN CSS -->
    <style>
    /* GOOGLE MATERIAL ICONS */
    /* https://fonts.google.com/icons */
    @font-face{font-family:"Material Icons";font-style:normal;font-weight:400;src:url(<?=HOST_ASSETS?>maticon.woff2) format("woff2");}
    .mi{font-family:"Material Icons";font-weight:400;font-style:normal;font-size:24px;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;word-wrap:normal;direction:ltr;-webkit-font-feature-settings:"liga";-webkit-font-smoothing:antialiased}
    .mi-big{font-size:32px}.mi-smol{font-size:18px}
    /* SHOW-HIDE */
    #cb-loading{transition:opacity .3s}.cb-hide{opacity:0;visibility:hidden;height:0}.cb-pg-hide{display:none}
    /* NOW LOADING */
    #cb-loading{width:100vw;height:100vh;position:fixed;top:0;left:0;z-index:999;background:rgba(0,0,0,.7)}#cb-loading .spinner-border{width:80px;height:80px}
    /* TOAST */
    #cb-toast{z-index:99}
    /* COMMON FORM */
    .searchBar{padding:0 .8rem .5rem .8rem}.cb-grey,.searchBar{background-color:#efefef}.zebra .row:nth-child(odd){background-color:#f2f2f2}
    </style>

    <!-- (A4) STORAGE BOXX CLIENT ENGINE -->
    <link rel="stylesheet" href="<?=HOST_ASSETS?>storage-boxx.css"/>
    <script>var cbhost={base:"<?=HOST_BASE?>",api:"<?=HOST_API_BASE?>",assets:"<?=HOST_ASSETS?>"};</script>
    <script async src="<?=HOST_ASSETS?>cb.js"></script>
  </head>
  <body>
    <!-- (B) COMMON SHARED INTERFACE -->
    <!-- (B1) NOW LOADING -->
    <div id="cb-loading" class="d-flex justify-content-center align-items-center cb-hide">
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- (B2) TOAST MESSAGE -->
    <div class="position-fixed bottom-0 end-0 p-3">
      <div id="cb-toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <span id="cb-toast-icon" class="mi"></span>
          <strong id="cb-toast-head" class="me-auto p-1"></strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div id="cb-toast-body" class="toast-body"></div>
      </div>
    </div>

    <!-- (B3) MODAL DIALOG BOX -->
    <div id="cb-modal" class="modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 id="cb-modal-head" class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="cb-modal-body" class="modal-body"></div>
      <div id="cb-modal-foot" class="modal-footer">
      </div>
    </div></div></div>

    <?php if ($_USER !== false) { ?>
    <!-- (C) MAIN NAV BAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container-fluid">
      <!-- (C1) BRANDING LOGO -->
      <a class="navbar-brand" href="<?=HOST_BASE?>">
        <img src="<?=HOST_ASSETS?>favicon.png" loading="lazy" width="32" height="32"/>
      </a>

      <!-- (C2) MENU TOGGLE BUTTON -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="mi">menu</span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- (C3) LEFT MENU ITEMS -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>">
              <span class="mi mi-smol">local_shipping</span> Movement
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>check">
              <span class="mi mi-smol">qr_code_scanner</span> Check
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>inventory">
              <span class="mi mi-smol">inventory_2</span> Items
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?=HOST_BASE?>users">
              <span class="mi mi-smol">people</span> Users
            </a>
          </li>
        </ul>

        <!-- (C4) RIGHT LOGOUT -->
        <button class="btn btn-danger btn-sm" onclick="cb.bye()">
          <span class="mi">logout</span>
        </button>
      </div>
    </div></nav>
    <?php } ?>

    <!-- (D) PAGES -->
    <div class="container pt-4">
      <div id="cb-page-1" class="cb-page">
