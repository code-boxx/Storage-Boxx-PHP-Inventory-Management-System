<?php
// (A) GET USER
$edit = isset($_POST["id"]) && $_POST["id"] != "";
if ($edit) {
  $user = $_CORE->autoCall("Users", "get");
}

// (B) USER FORM 
?>

<style>
  .profileimg {
    cursor: pointer;
    background-color: #0d6efd;
    color: #ffffff;
    padding: 10px 20px;
    border-color: #0d6efd;
    border-radius: .3rem;
  }

  #profileimg {
    opacity: 0;
    position: relative;
    z-index: 2;
  }
</style>
<h3 class="mb-3"><?= $edit ? "EDIT" : "ADD" ?> USER</h3>
<form onsubmit="return usr.save()" enctype="multipart/form-data">
  <input type="hidden" id="user_id" value="<?= isset($user) ? $user["user_id"] : "" ?>" />
  <div class="bg-white border p-4 mb-3">
    <div class="center input-group mb-3">
      <div class="input-group-prepend">
        <label for="profileimg" class="profileimg" style="position: absolute; right: 0; top: 75%;">Upload Profile Picture</label>
        <img style="max-width: 200px; max-height: 200px; min-width: 200px; min-height: 200px;" id="preview" class="img-thumbnail" src="<?= isset($user) && $user['user_profilepic'] ? './images/profileimg/' . $user['user_profilepic'] : './images/profileimg/default.png' ?>" alt="event-thumbnail" />
        <input type="file" name="profileimg" id="profileimg" hidden accept="image/png, image/jpeg" onclick="usr.fileUpload()" />
      </div>
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">person</span>
      </div>
      <input type="text" class="form-control" id="user_name" required value="<?= isset($user) ? $user["user_name"] : "" ?>" placeholder="Name" />
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">email</span>
      </div>
      <input type="email" class="form-control" id="user_email" required value="<?= isset($user) ? $user["user_email"] : "" ?>" placeholder="Email" />
    </div>

    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text mi">lock</span>
      </div>
      <input type="password" id="user_password" class="form-control" placeholder="Password" required />
    </div>
  </div>

  <input type="button" class="col btn btn-danger btn-lg" value="Back" onclick="cb.page(1)" />
  <input type="submit" name="submit" class="col btn btn-primary btn-lg" value="Save" />
</form>