<form action="?" method="post">
  <ul class="list-group">
    <li class="list-group-item fs-5 fw-bold active">Admin Panel</li>
    <li class="list-group-item">
      <div class="input-group mb-3">
        <input type="password" class="form-control" name="pass" placeholder="{$password}" aria-label="{$password}"
          id="passwordField">
        <button class="btn btn-secondary" type="button" id="show">SHOW</button>
      </div>
      <div class="mb-2">
        <input type="checkbox" class="form-check-input" name="r" value="1" id="r">
        <label class="form-check-label" for="r">{$remember}</label>
      </div>
      <div class="text-center">
        <input type="submit" class="btn btn-dark px-4" value="{$login}"></input>
      </div>
    </li>
  </ul>
  <input type='hidden' name='token' value='{$token}'>
</form>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var passwordField = document.getElementById("passwordField");
    var showButton = document.getElementById("show");

    showButton.addEventListener("click", function () {
      if (passwordField.type === "password") {
        passwordField.type = "text";
        showButton.textContent = "HIDE";
      } else {
        passwordField.type = "password";
        showButton.textContent = "SHOW";
      }
    });
  });
</script>