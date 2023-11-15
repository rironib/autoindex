{$add}
<form action='?' method='post'>
<div class="list-group">
    <div class="list-group-item fs-5 fw-bold active">{$lgrequest}</div>
    <div class="list-group-item">
        <div class="mb-2">
            <textarea class="form-control" placeholder="Write your request here..." name='rq' style="height: 100px"></textarea>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-dark px-4" value="{$lgrequest}">
        </div>
    </div>
</div>
</form>

<div class='list-group mb-2'>
    {$requests}
</div>

<div class="d-flex justify-content-center pagination m-2">
    {$show_pages}
</div>