{$add}
<form action='?' method='post'>
    <div class="list-group mb-2">
        <div class="list-group-item fs-5 fw-bold active">{$lgrequest}</div>
        <div class="list-group-item">
            <div class="mb-2">
                <textarea class="form-control" rows="5" placeholder="Write your request here..." name='rq'
                    style="height: 100px"></textarea>
            </div>
            <div class="text-center">
                <input type="submit" class="btn btn-dark px-4" value="{$lgrequest}">
            </div>
        </div>
    </div>
</form>

{$requests}

<div class="pagination justify-content-center m-2">
    {$show_pages}
</div>