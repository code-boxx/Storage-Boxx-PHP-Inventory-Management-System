function login () {
  cb.api({
    mod : "session", req : "login",
    data : {
      email : document.getElementById("login-email").value,
      password : document.getElementById("login-pass").value
    },
    passmsg : false,
    onpass : () => { location.href = cbhost.base; }
  });
  return false;
}