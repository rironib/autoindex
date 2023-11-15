<form action="?" method="post">
    <ul class="list-group">
        <li class="list-group-item fs-5 fw-bold active">Admin Panel</li>
        <li class="list-group-item">
            <input type="text" class="form-control" name="user" placeholder="{$username}">
        </li>
        <li class="list-group-item">
            <input type="password" class="form-control" name="pass" placeholder="{$password}">
        </li>
        <li class="list-group-item">
            <input type="checkbox" class="form-check-input" name="r" value="1" id="r">
            <label class="form-check-label" for="r">{$remember}</label>
        </li>
    </ul>
    <div class="my-3 text-center">
        <input type="submit" class="btn btn-dark px-4" value="{$login}"></input>
    </div>
    <input type='hidden' name='token' value='{$token}'>
</form>