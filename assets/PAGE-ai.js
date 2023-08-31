var chat = {
  // (A) SETTINGS & FLAGS
  host : "ws://localhost:5678/", socket : null,
  hMsg : null, hQn : null,
  hTxt : null, hSR : null, hGo : null,

  // (B) INIT
  init : () => {
    // (B1) GET HTML ELEMENTS
    chat.hMsg = document.getElementById("ai-chat");
    chat.hQn = document.getElementById("ai-query");
    chat.hTxt = document.getElementById("ai-txt");
    chat.hSR = document.getElementById("ai-sr");
    chat.hGo = document.getElementById("ai-go");

    // (B2) CONNECT TO WEBSOCKET
    chat.socket = new WebSocket(chat.host);

    // (B3) ON CONNECT - ENABLE QUERY FORM
    chat.socket.addEventListener("open", () => {
      chat.controls(1);
      chat.draw("Ready!", "sys");
    });

    // (B4) ON RECEIVE MESSAGE - DRAW IN HTML
    chat.socket.addEventListener("message", e => chat.draw(e.data, "bot"));

    // (B5) ON ERROR & CONNECTION LOST
    chat.socket.addEventListener("close", () => {
      chat.controls();
      chat.draw("Websocket connection lost!", "sys");
    });
    chat.socket.addEventListener("error", err => {
      chat.controls();
      console.log(err);
      chat.draw("Websocket connection error!", "sys");
    });
  },

   // (C) TOGGLE HTML CONTROLS
   controls : enable => {
    if (enable) {
      chat.hTxt.disabled = false;
      chat.hSR.disabled = false;
      chat.hGo.disabled = false;
    } else {
      chat.hTxt.disabled = true;
      chat.hSR.disabled = true;
      chat.hGo.disabled = true;
    }
  },

  // (D) SEND MESSAGE TO CHAT SERVER
  send : () => {
    chat.controls(); // disable question form until bot replies
    chat.draw(chat.hTxt.value, "human");
    chat.socket.send(chat.hTxt.value);
    chat.hTxt.value = "";
    return false;
  },

  // (E) DRAW MESSAGE IN HTML
  draw : (msg, css) => {
    let row = document.createElement("div");
    row.className = "ai-" + css;
    row.innerHTML = `<div class="chatName">${css}</div> <div class="chatMsg">${msg}</div>`;
    chat.hMsg.appendChild(row);
    row.classList.add("ai-show");
    if (css=="bot") { chat.controls(1); } // enable form on bot reply
  }
};
window.addEventListener("load", chat.init);