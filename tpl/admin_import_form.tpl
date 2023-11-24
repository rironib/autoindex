{$message}

<form action='?' method='post'>

    <div class='list-group mb-2'>
        <div class='list-group-item fs-5 fw-bold active'>{$import_files}</div>
        <div class='list-group-item'>
            <select class='form-control' name='path'>
                <option value=''>./</option>
                {$path_opt}
            </select>
        </div>
        <div class='list-group-item'>
            <input type='url' class='form-control' name='f[]' placeholder='{$url}'>
        </div>
        <div class='list-group-item bg-secondary-subtle'>
            <input type='text' class='form-control' name='n[]' placeholder='{$name}'>
        </div>
        <div class='list-group-item'>
            <input type='url' class='form-control' name='f[]' placeholder='{$url}'>
        </div>
        <div class='list-group-item bg-secondary-subtle'>
            <input type='text' class='form-control' name='n[]' placeholder='{$name}'>
        </div>
        <div class='list-group-item'>
            <input type='url' class='form-control' name='f[]' placeholder='{$url}'>
        </div>
        <div class='list-group-item bg-secondary-subtle'>
            <input type='text' class='form-control' name='n[]' placeholder='{$name}'>
        </div>
        <div class='list-group-item'>
            <input type='url' class='form-control' name='f[]' placeholder='{$url}'>
        </div>
        <div class='list-group-item bg-secondary-subtle'>
            <input type='text' class='form-control' name='n[]' placeholder='{$name}'>
        </div>
        <div class='list-group-item'>
            <input type='url' class='form-control' name='f[]' placeholder='{$url}'>
        </div>
        <div class='list-group-item bg-secondary-subtle'>
            <input type='text' class='form-control' name='n[]' placeholder='{$name}'>
        </div>
    </div>

    <div class='text-center mb-2'>
        <input type='submit' class='btn btn-dark px-4' value='{$import_files}'>
    </div>
</form>

<div class='alert alert-info' role='alert'>
    {$max_file_size} 64 MB
</div>