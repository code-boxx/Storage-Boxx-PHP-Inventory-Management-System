function signin () {
  cb.api({
    mod : "session", req : "login",
    data : {
      email : document.getElementById("user_email").value,
      password : document.getElementById("user_password").value
    },
    passmsg : false,
    onpass : () => { location.href = cbhost.base; }
  });
  return false;
}
