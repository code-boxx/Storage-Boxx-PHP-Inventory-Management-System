function send () {
  cb.api({
    mod : "push", act : "send",
    data : {
      title : document.getElementById("push_title").value,
      body : document.getElementById("push_txt").value,
      icon : document.getElementById("push_ico").value,
      image : document.getElementById("push_img").value
    },
    passmsg : "Message sent",
    onpass : () => document.getElementById("push_form").reset()
  });
  return false;
}