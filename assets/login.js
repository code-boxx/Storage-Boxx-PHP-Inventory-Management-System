function signin () {
  sb.api({
    mod : "session", req : "login",
    data : {
      email : document.getElementById("user_email").value,
      password : document.getElementById("user_password").value
    },
    passmsg : false,
    onpass : function () { location.href = sbhost.base; }
  });
  return false;
}
