{$message}

<form action='?' method='post'
enctype='multipart/form-data'>
    <div class='list-group mb-2'>
        <div class='list-group-item fs-5 fw-bold active'>{$upload_files}</div>
        <div class='list-group-item'>
            <select class='form-control' name='path'>
                <option value=''>./</option>
                {$path_opt}
            </select>
        </div>
        <div class='list-group-item'>
            <input class='form-control' type='file' name='f[]' multiple>
        </div>
    </div>
    <div class='text-center mb-2'>
        <input type='submit' class='btn btn-dark px-4' value='{$upload_files}'>
    </div>
</form>

<div class='alert alert-info' role='alert'>
  {$max_file_size}
</div>