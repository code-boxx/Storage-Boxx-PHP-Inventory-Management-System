function forgot () {
  cb.api({
    mod : "session", act : "forgotA",
    data : { email : document.getElementById("forgot-email").value },
    passmsg : false,
    onpass : () => cb.modal("Reset Link Sent", "Click on the reset link in your email.")
  });
  return false;
}