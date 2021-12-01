var sb = {
  // (A) HTML INTERFACE
  // (A1) LOADING SPINNER
  //  show : boolean, show or hide spinner
  loady : null,
  loading : (show) => {
    if (show) {
      sb.loady.classList.remove("sb-hide");
    } else {
      sb.loady.classList.add("sb-hide");
    }
  },

  // (A2) TOAST MESSAGE
  //  status : boolean, success or failure icon
  //  head : string, title text
  //  body : string, body text
  toasty : null,
  toast : (status, head, body) => {
    // GET SECTIONS
    let ticon = document.getElementById("sb-toast-icon"),
        thead = document.getElementById("sb-toast-head"),
        tbody = document.getElementById("sb-toast-body");

    // SET ICON HEADER BODY
    if (status==1 || status=="1" || status==true) { ticon.innerHTML = "thumb_up"; }
    else if (status==0 || status=="0" || status==false) { ticon.innerHTML = "error"; }
    else { ticon.innerHTML = "help"; }
    thead.innerHTML = head;
    tbody.innerHTML = body;

    // SHOW
    sb.toasty.show();
  },

  // (A3) MODAL DIALOG BOX
  //  head : string, title text
  //  body : string, body text
  //  foot : string, bottom text (function to auto generate yes/no buttons)
  mody : null,
  modal : (head, body, foot) => {
    // GET SECTIONS
    let mhead = document.getElementById("sb-modal-head"),
        mbody = document.getElementById("sb-modal-body"),
        mfoot = document.getElementById("sb-modal-foot");

    // SET HEADER & BODY
    if (head===undefined || head===null) { head = ""; }
    if (body===undefined || body===null) { body = ""; }
    mhead.innerHTML = head;
    mbody.innerHTML = body;

    // SET FOOTER
    if (foot===undefined || foot===null) { foot = ""; }
    if (typeof foot == "function") {
      // AUTO GENERATE NO BUTTON
      mfoot.innerHTML = "";
      let btn = document.createElement("button");
      btn.className = "btn btn-danger";
      btn.innerHTML = "No";
      btn.setAttribute("data-bs-dismiss", "modal");
      mfoot.appendChild(btn);

      // AUTO GENERATE YES BUTTON
      btn = document.createElement("button");
      btn.className = "btn btn-primary";
      btn.innerHTML = "Yes";
      btn.setAttribute("data-bs-dismiss", "modal");
      btn.addEventListener("click", foot);
      mfoot.appendChild(btn);
    } else {
      // SET TEXT
      mfoot.innerHTML = foot;
    }

    // SHOW
    sb.mody.show();
  },

  // (A4) CHANGE "LOCAL" PAGE
  //  num : int, page number (1 to 3)
  page : (num) => {
    for (let i=1; i<=3; i++) {
      let pg = document.getElementById("sb-page-"+i);
      if (i==num) {
        pg.classList.remove("sb-pg-hide");
      } else {
        pg.classList.add("sb-pg-hide");
      }
    }
  },

  // (B) AJAX CALL
  //  url : string, target URL
  //  data : optional object, data to send
  //  onpass : function, run this function on server response
  //  onerr : optional function, run this function on error
  //  loading : boolean, show "now loading" screen? default true.
  ajax : (opt) => {
    // (B1) CHECKS
    let err = null;
    if (opt.url === undefined) { err = "Target URL is not set!"; }
    if (opt.onpass === undefined) { err = "Function to call on onpass is not set!"; }
    if (err !== null) {
      sb.modal("AJAX ERROR", err);
      return false;
    }

    // (B2) SET DEFAULTS
    if (opt.loading === undefined) { opt.loading = true; }
    if (opt.debug === undefined) { opt.debug = false; }

    // (B4) DATA TO SEND
    var data = new FormData();
    for (var key in opt.data) { data.append(key, opt.data[key]); }

    // (B5) AJAX REQUEST
    if (opt.loading) { sb.loading(1); } // NOW LOADING
    fetch(opt.url, { method:"POST", credentials:"include", body:data })
    .then((res) => {
      if (res.status==200) { return res.text(); }
      else {
        sb.modal("SERVER ERROR", "Bad server response - " + res.status);
        console.error(res.status, res);
        if (opt.onerr) { opt.onerr(); }
      }
    })
    .then((txt) => { opt.onpass(txt); })
    .catch((err) => {
      sb.modal("AJAX ERROR", err.message);
      console.error(err);
      if (opt.onerr) { opt.onerr(); }
    })
    .finally(() => {
      if (opt.loading) { sb.loading(0); } // DONE LOADING
    });
  },

  // (C) DO AN AJAX API CALL
  //  mod : string, module to call
  //  req : string, request
  //  data : object, data to send as above
  //  loading : object, show loading screen as above?
  //  passmsg : boolean false to supress toast "success message".
  //            boolean true to use server response message.
  //            string to override "OK" message.
  //  nofail : boolean, supress modal "failure message"? Default false.
  //  onpass : optional function, run this on API response pass.
  //  onfail : optional function, run this on API response fail.
  api : (opt) => {
    // (C1) INIT OPTIONS
    var options = {};
    options.url = sbhost.api + `${opt.mod}/${opt.req}/`;
    if (opt.data) { options.data = opt.data; }
    if (opt.loading) { options.loading = opt.loading; }
    if (opt.passmsg === undefined) { opt.passmsg = "OK"; }
    if (opt.nofail === undefined) { opt.nofail = false; }

    // (C2) ON AJAX LOAD
    options.onpass = (res) => {
      // PARSE RESULTS
      try { var res = JSON.parse(res); }
      catch (err) {
        console.error(res);
        sb.modal("AJAX ERROR", "Failed to parse JSON data.");
        return false;
      }

      // RESULTS FEEBACK
      if (res.status=="E") { location.href = sbhost.base + "login/"; }
      else if (res.status) {
        if (opt.passmsg !== false) {
          sb.toast(1, "Success",
            opt.passmsg===true ? res.message : opt.passmsg
          );
        }
        if (opt.onpass) { opt.onpass(res); }
      } else {
        if (!opt.nofail) { sb.modal("ERROR", res.message); }
        if (opt.onfail) { opt.onfail(); }
      }
    };

    // (C3) GO!
    sb.ajax(options);
  },

  // (D) AJAX LOAD HTML PAGE
  //  page : string, http://site.com/a/PAGE/
  //  target : string, ID of target HTML element
  //  data : object, data to send as above
  //  loading : boolean, show loading screen as above. Default false.
  //  onload : optional function, do this on loaded
  load : (opt) => {
    // (D1) INIT OPTIONS
    var options = {};
    options.url = sbhost.base + `a/${opt.page}/`;
    options.loading = opt.loading ? opt.loading : false;
    if (opt.data) { options.data = opt.data; }

    // (D2) ON AJAX LOAD
    options.onpass = (res) => {
      if (res=="SE") { location.href = sbhost.base + "login/"; }
      else {
        document.getElementById(opt.target).innerHTML = res;
        if (opt.onload) { opt.onload(); }
      }
    };

    // (D3) GO!
    sb.ajax(options);
  },

  // (E) SIGN OFF
  //  confirm : boolean, confirmed sign off
  bye : (confirm) => {
    if (confirm) {
      sb.api({
        mod : "session", req : "logout",
        nopass : true,
        onpass : () => { location.href = sbhost.base + "login/"; }
      });
    } else {
      sb.modal("Please Confirm", "Sign off?", () => { sb.bye(true); });
    }
  }
};

// (F) INIT INTERFACE
window.addEventListener("load", () => {
  sb.loady = document.getElementById("sb-loading");
  sb.toasty = new bootstrap.Toast(document.getElementById("sb-toast"), {
    delay: 3500
  });
  sb.mody = new bootstrap.Modal(document.getElementById("sb-modal"));
});
