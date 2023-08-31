var voice = {
  // (A) INIT SPEECH RECOGNITION
  recog : null, // speech recognition object
  init : () => {
    navigator.mediaDevices.getUserMedia({ audio: true })
    .then(stream => {
      // (A1) SPEECH RECOGNITION OBJECT + SETTINGS
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      voice.recog = new SpeechRecognition();
      voice.recog.lang = "en-US";
      voice.recog.continuous = false;
      voice.recog.interimResults = false;

      // (A2) POPUPLATE QUERY FIELD ON SPEECH RECOGNITION
      voice.recog.onresult = evt => {
        let said = evt.results[0][0].transcript.toLowerCase();
        chat.hTxt.value = said;
        voice.stop();
        chat.send();
      };

      // (A3) ON SPEECH RECOGNITION ERROR
      voice.recog.onerror = err => {
        console.error(err);
        chat.draw("Speech recognition error!", "sys");
      };

      // (A4) READY!
      chat.hSR.onclick = voice.start;
      chat.hSR.classList.remove("d-none");
      voice.stop();
    })
    .catch(err => console.error(err));
  },

  // (B) START SPEECH RECOGNITION
  start : () => {
    voice.recog.start();
    chat.hSR.onclick = voice.stop;
    chat.hSR.classList.remove("btn-primary");
    chat.hSR.classList.add("btn-success");
    // chat.draw("Speak into the microphone, or click again to cancel.", "sys");
  },

  // (C) STOP/CANCEL SPEECH RECOGNITION
  stop : () => {
    voice.recog.stop();
    chat.hSR.onclick = voice.start;
    chat.hSR.classList.remove("btn-success");
    chat.hSR.classList.add("btn-primary");
  }
};
window.addEventListener("load", voice.init);