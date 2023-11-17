<form action='?'>
<div class="input-group my-3">
  <input type="text" class="form-control" name="search" value="{$search_text}" placeholder="{$search_placeholder}" aria-label="{$search_text}" aria-describedby="button-addon2">
  <button class="btn btn-dark" type="submit" id="button-addon2">{$search}</button>
</div>
</form>

<div class="list-group mb-2">{$updates}</div>

<div class="list-group mb-2">
  <div class="list-group-item fs-5 fw-bold active">{$downloads_menu}</div>
  <div class="list-group-item bg-secondary-subtle text-center">{$show_order}</div>
  {$description}
  {$_admin2}
  {$folders}
  {$files}
</div>
<div class="pagination justify-content-center m-2">
    {$show_pages}
</div>

<div class="list-group mb-2">
  <div class="list-group-item fs-5 fw-bold active">{$extra}</div>
  <div class="list-group-item"><a href="{$url}/usr_set"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;{$settings}</a></div>
  <div class="list-group-item"><a href="{$url}/request"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;{$request}</a></div>
  <div class="list-group-item"><a href="{$url}/tos"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;{$terms_of_service}</a></div>
  <div class="list-group-item"><a href="{$url}/disclaimer"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;Disclaimer</a></div>
  <div class="list-group-item"><a href="{$url}/sitemap.xml"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;Sitemap</a></div>
  <div class="list-group-item"><a href="{$url}/feed"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;RSS Feed</a></div>
  <div class="list-group-item"><a href="{$url}/top.php"><img src="{$MAI_TPL}style/images/gdir.gif" alt="."/>&nbsp;Top Files</a></div>
</div>
{$sort_remove}