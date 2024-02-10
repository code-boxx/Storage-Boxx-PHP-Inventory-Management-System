function save () {
  // (A) GET ALL DATA
  let data = {};
  for (let i of document.querySelectorAll("#set-list .form-control")) {
    data[i.name] = i.value;
  }

  // (B) API CALL
  cb.api({
    mod : "settings", act : "save",
    data : { settings : JSON.stringify(data) },
    passmsg : "Settings Saved"
  });
  return false;
}