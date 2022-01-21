function save () {
  // (A) GET ALL DATA
  let data = {};
  for (let i of document.querySelectorAll("#set-list input[type=text]")) {
    data[i.name] = i.value;
  }

  // (B) API CALL
  cb.api({
    mod : "options", req : "save",
    data : {options: JSON.stringify(data)},
    passmsg : "Settings Saved"
  });
  return false;
}
