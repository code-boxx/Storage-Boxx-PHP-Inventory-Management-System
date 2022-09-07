<?php
class Push extends Core {
  // (A) SAVE SUBSCRIBER
  function save ($endpoint, $sub) {
    $this->DB->replace("webpush", ["endpoint", "data"], [$endpoint, $sub]);
    return true;
  }

  // (B) REMOVE SUBSCRIBER
  function del ($endpoint) {
    $this->DB->delete("webpush", "`endpoint`=?", [$endpoint]);
    return true;
  }

  // (C) SEND PUSH
  function send ($title, $body, $icon=null, $image=null) {
    // (C1) LOAD WEB PUSH LIBRARY
    require PATH_LIB . "webpush/autoload.php";
    $push = new Minishlink\WebPush\WebPush(["VAPID" => [
      "subject" => EMAIL_FROM,
      "publicKey" => PUSH_PUBLIC,
      "privateKey" => PUSH_PRIVATE
    ]]);

    // (C2) SEND TO SUBSCRIBERS
    $this->DB->query("SELECT `data` FROM `webpush`");
    while ($r = $this->DB->stmt->fetchColumn()) {
      // (C2-1) SUBSCRIBER
      $sub = Minishlink\WebPush\Subscription::create(json_decode($r, true));

      // (C2-2) PUSH
      $result = $push->sendOneNotification($sub, json_encode([
        "title" => $title,
        "body" => $body,
        "icon" => $icon,
        "image" => $image
      ]));

      // (C2-3) RESULT
      if (!$result->isSuccess()) {
        $this->del($result->getRequest()->getUri()->__toString());
      }
    }

    // (C3) DONE
    return true;
  }
}