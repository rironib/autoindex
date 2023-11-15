<form action="?" method="post">
    <ul class="list-group">
        <li class="list-group-item fs-5 fw-bold active">Admin Panel</li>
        <li class="list-group-item">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="{$username}">
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="{$password}">
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