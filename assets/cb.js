var cb = {
  // (A) HTML INTERFACE
  // (A1) LOADING SPINNER
  //  show : boolean, show or hide spinner
  loady : null,
  loading : (show) => {
    if (show) { cb.loady.classList.remove("cb-hide"); }
    else { cb.loady.classList.add("cb-hide"); }
  },

  // (A2) TOAST MESSAGE
  //  status : boolean, success or failure icon
  //  head : string, title text
  //  body : string, body text
  toasty : null,
  toast : (status, head, body) => {
    // GET SECTIONS
    let ticon = document.getElementById("cb-toast-icon"),
        thead = document.getElementById("cb-toast-head"),
        tbody = document.getElementById("cb-toast-body");

    // SET ICON HEADER BODY
    if (status==1 || status=="1" || status==true) { ticon.innerHTML = "thumb_up"; }
    else if (status==0 || status=="0" || status==false) { ticon.innerHTML = "error"; }
    else { ticon.innerHTML = "help"; }
    thead.innerHTML = head;
    tbody.innerHTML = body;

    // SHOW
    cb.toasty.show();
  },

  // (A3) MODAL DIALOG BOX
  //  head : string, title text
  //  body : string, body text
  //  foot : string, bottom text (function to auto generate yes/no buttons)
  mody : null,
  modal : (head, body, foot) => {
    // GET SECTIONS
    let mhead = document.getElementById("cb-modal-head"),
        mbody = document.getElementById("cb-modal-body"),
        mfoot = document.getElementById("cb-modal-foot");

    // SET HEADER & BODY
    if (head===undefined || head===null) { head = ""; }
    if (body===undefined || body===null) { body = ""; }
    mhead.innerHTML = head;
    mbody.innerHTML = body;

    // SET FOOTER (NONE)
    if (foot===undefined || foot===null) { foot = ""; }

    // SET FOOTER (FUNCTION)
    else if (typeof foot == "function") {
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
    }

    // SET FOOTER (STRING)
    else { mfoot.innerHTML = foot; }

    // SHOW
    cb.mody.show();
  },

  // (A4) CHANGE "LOCAL" PAGE
  //  num : int, page number (1 to 5)
  page : (num) => {
    for (let i=1; i<=5; i++) {
      let pg = document.getElementById("cb-page-"+i);
      if (i==num) { pg.classList.remove("cb-pg-hide"); }
      else { pg.classList.add("cb-pg-hide"); }
    }
  },

  // (B) AJAX CALL
  //  url : string, target URL
  //  data : optional object, data to send
  //  loading : boolean, show "now loading" screen? default true.
  //  debug : boolean, debug mode. default false.
  //  onpass : function, run this function on server response
  //  onerr : optional function, run this function on error
  ajax : (opt) => {
    // (B1) CHECKS
    let err = null;
    if (opt.url === undefined) { err = "Target URL is not set!"; }
    if (opt.onpass === undefined) { err = "Function to call on onpass is not set!"; }
    if (err !== null) {
      cb.modal("AJAX ERROR", err);
      return false;
    }

    // (B2) SET DEFAULTS
    if (opt.loading === undefined) { opt.loading = true; }
    if (opt.debug === undefined) { opt.debug = false; }

    // (B4) DATA TO SEND
    var data = new FormData();
    for (var key in opt.data) { data.append(key, opt.data[key]); }

    // (B5) AJAX REQUEST
    if (opt.loading) { cb.loading(true); } // NOW LOADING
    fetch(opt.url, { method:"POST", credentials:"include", body:data })
    .then((res) => {
      if (opt.debug) { console.log(res); }
      if (res.status==200) { return res.text(); }
      else {
        cb.modal("SERVER ERROR", "Bad server response - " + res.status);
        console.error(res.status, res);
        if (opt.onerr) { opt.onerr(); }
      }
    })
    .then((txt) => {
      if (opt.debug) { console.log(txt); }
      opt.onpass(txt);
    })
    .catch((err) => {
      cb.modal("AJAX ERROR", err.message);
      console.error(err);
      if (opt.onerr) { opt.onerr(); }
    })
    .finally(() => {
      if (opt.loading) { cb.loading(false); } // DONE LOADING
    });
  },

  // (C) DO AN AJAX API CALL
  //  mod : string, module to call
  //  req : string, request
  //  data : object, data to send
  //  loading : boolean, show loading screen?
  //  debug : boolean, optional debug mode. default false.
  //  passmsg : boolean false to supress toast "success message".
  //            boolean true to use server response message.
  //            string to override "OK" message.
  //  nofail : boolean, supress modal "failure message"? Default false.
  //  onpass : optional function, run this on API response pass.
  //  onfail : optional function, run this on API response fail.
  //  onerr : optional function, run this on ajax call error.
  api : (opt) => {
    // (C1) INIT OPTIONS
    var options = {};
    options.url = cbhost.api + `${opt.mod}/${opt.req}/`;
    if (opt.data) { options.data = opt.data; }
    if (opt.loading) { options.loading = opt.loading; }
    if (opt.debug) { options.debug = opt.debug; }
    if (opt.onerr) { options.onerr = opt.onerr; }
    if (opt.passmsg === undefined) { opt.passmsg = "OK"; }
    if (opt.nofail === undefined) { opt.nofail = false; }

    // (C2) ON AJAX LOAD
    options.onpass = (res) => {
      // PARSE RESULTS
      try { var res = JSON.parse(res); }
      catch (err) {
        console.error(res);
        cb.modal("AJAX ERROR", "Failed to parse JSON data.");
        return false;
      }

      // RESULTS FEEBACK
      if (res.status=="E") { location.href = cbhost.base + "login/"; }
      else if (res.status) {
        if (opt.passmsg !== false) {
          cb.toast(1, "Success", opt.passmsg===true ? res.message : opt.passmsg);
        }
        if (opt.onpass) { opt.onpass(res); }
      } else {
        if (!opt.nofail) { cb.modal("ERROR", res.message); }
        if (opt.onfail) { opt.onfail(res.message); }
      }
    };

    // (C3) GO!
    cb.ajax(options);
  },

  // (D) AJAX LOAD HTML PAGE
  //  page : string, http://site.com/a/PAGE/
  //  target : string, ID of target HTML element
  //  data : object, data to send
  //  loading : boolean, show loading screen? Default false.
  //  debug : boolean, optional debug mode. default false.
  //  onload : optional function, do this on loaded
  //  onerr : optional function, do this on ajax error
  load : (opt) => {
    // (D1) INIT OPTIONS
    var options = {};
    options.url = cbhost.base + `a/${opt.page}/`;
    options.loading = opt.loading ? opt.loading : false;
    if (opt.debug) { options.debug = opt.debug; }
    if (opt.onerr) { options.onerr = opt.onerr; }
    if (opt.data) { options.data = opt.data; }

    // (D2) ON AJAX LOAD
    options.onpass = (res) => {
      if (res=="SE") { location.href = cbhost.base + "login/"; }
      else {
        document.getElementById(opt.target).innerHTML = res;
        if (opt.onload) { opt.onload(); }
      }
    };

    // (D3) GO!
    cb.ajax(options);
  },

  // (E) SIGN OFF
  //  confirm : boolean, confirmed sign off
  bye : (confirm) => {
    if (confirm) {
      cb.api({
        mod : "session", req : "logout",
        nopass : true,
        onpass : () => { location.href = cbhost.base + "login/"; }
      });
    } else {
      cb.modal("Please Confirm", "Sign off?", () => { cb.bye(true); });
    }
  }
};

// (F) INIT INTERFACE
window.addEventListener("load", () => {
  cb.loady = document.getElementById("cb-loading");
  cb.toasty = new bootstrap.Toast(document.getElementById("cb-toast"), { delay: 3500 });
  cb.mody = new bootstrap.Modal(document.getElementById("cb-modal"));
});
