var helper = {
  // (A) ARRAY BUFFER TO BASE 64
  atb : b => {
    let u = new Uint8Array(b), s = "";
    for (let i=0; i<u.byteLength; i++) { s += String.fromCharCode(u[i]); }
    return btoa(s);
  },
 
  // (B) HELPER - BASE 64 TO ARRAY BUFFER
  bta : o => {
    let pre = "=?BINARY?B?", suf = "?=";
    for (let k in o) { if (typeof o[k] == "string") {
      let s = o[k];
      if (s.substring(0, pre.length)==pre && s.substring(s.length - suf.length)==suf) {
        let b = window.atob(s.substring(pre.length, s.length - suf.length)),
        u = new Uint8Array(b.length);
        for (let i=0; i<b.length; i++) { u[i] = b.charCodeAt(i); }
        o[k] = u.buffer;
      }
    } else { helper.bta(o[k]); }}
  }
};