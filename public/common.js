var common = {
  // (A) LOADING () : TOGGLE "NOW LOADING" BLOCK
  //  show : 1 to show, 0 to hide
  loading : function (show) {
    var block = document.getElementById("page-loader");
    if (show) { block.classList.add("open"); }
    else { block.classList.remove("open"); }
  },

  // (B) TOAST
  ttimer : null, // TOAST TIMER
  toast : function (message) {
    var t = document.getElementById("page-toast");
    if (message === undefined) {
      common.ttimer = null;
      t.classList.remove("open");
    } else {
      if (common.ttimer!==null) { clearTimeout(common.ttimer); }
      t.innerHTML = message;
      t.classList.add("open");
      common.ttimer = setTimeout(common.toast, 2000);
    }
  },

  // (C) TOGGLE SIDEBAR
  side : function () {
    document.getElementById("page-sidebar").classList.toggle("open");
  },

  // (D) PAGE () : SWITCH INTERFACE PAGE
  //  pg : A to E, HTML div to switch to
  page : function (pg) {
    var all = document.getElementsByClassName("page-body");
    for (var el of all) { el.style.display = "none"; }
    document.getElementById("page" + pg).style.display = "block";
  },

  // (E) AJAX () : DO AN AJAX CALL
  // opt : object, options
  //   debug : debug mode
  //   block : block the page with "now loading"?
  //   url : URL to call
  //   data : object, data to send
  //   target : optional, ID of target HTML element
  //            will put server response into this element
  //   onpass : do this on server response OK
  //   onfail : do this on server response FAIL
  //   onerror : no server response
  //   apass & afail :
  //     true : show server response in alert()
  //     false : silent
  //     string : show this message in toast instead
  ajax : function (opt) {
    // (E1) DEFAULTS - DEBUG OFF, BLOCK PAGE, ALERT ON
    if (opt.debug === undefined) { opt.debug = false; }
    if (opt.block === undefined) { opt.block = true; }
    if (opt.apass === undefined) { opt.apass = true; }
    if (opt.afail === undefined) { opt.afail = true; }
    
    // (E2) "NOW LOADING"
    if (opt.block) { common.loading(1); }

    // (E3) APPEND FORM DATA
    var data = new FormData();
    for (var key in opt.data) { data.append(key, opt.data[key]); }
    if (opt.target) { data.append("ajax", 1); }

    // (E4) AJAX CALL
    var xhr = new XMLHttpRequest();
    xhr.open('POST', opt.url);
    xhr.onload = function () {
      // (E4A) DEBUG MODE
      if (opt.debug) {
        console.log(this.response);
        console.log(this.status);
      }

      // (E4B) SESSION EXPIRED OR NO PERMISSION
      if (this.response=="BADSESS") {
        alert("Session has expired or insufficient permission. Please reload page.");
      }

      // (E4C) SERVER RESPOND OK
      else if (this.status == 200) {
        if (opt.target) {
          document.getElementById(opt.target).innerHTML = this.response;
          if (typeof opt.onpass == "function") { opt.onpass(res); }
        } else {
          var res = JSON.parse(this.response);
          if (res.status) {
            if (opt.apass === true) { alert(res.message); }
            if (typeof opt.apass == "string") { common.toast(opt.apass); }
            if (typeof opt.onpass == "function") { opt.onpass(res); }
          } else {
            if (opt.afail === true) { alert(res.message); }
            if (typeof opt.afail == "string") { common.toast(opt.afail); }
            if (typeof opt.onfail == "function") { opt.onfail(res); }
          }
        }
      }

      // (E4D) NO RESPONSE OR ERROR
      else {
        if (opt.afail) { alert(`${this.status} : ERROR LOADING ${opt.url}!`); }
        if (typeof opt.onerror == "function") {
          opt.onerror(this.response, xhr.status);
        }
      }

      // (E4E) CLOSE "NOW LOADING"
      if (opt.block) { common.loading(0); }
    };
    xhr.send(data);
  },

  // (F) BYE () : SIGN OUT
  bye : function () { if (confirm("Sign Off?")) {
    common.ajax({
      url : urlapi + "User",
      data : { req : "logoff" },
      apass : false,
      onpass : function () { location.href = urlroot + "login"; }
    });
  }}
};