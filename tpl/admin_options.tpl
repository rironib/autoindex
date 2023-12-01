{$update_av}

<div class="list-group mb-2">
    <div class="list-group-item fs-5 fw-bold active">Admin Panel</div>
    <div class="list-group-item">{$mark} <a href='{$url}'>{$file_manager}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/upload.php'>{$upload_files}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/import.php'>{$import_files}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/request.php'>{$request}</a> <span class='badge bg-primary'>{$request_new}</span></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/actions.php?act=editset'>{$settings}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/plugin_manager.php'>{$plugin_manager}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/update_manager.php'>Updates Manager</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/scan.php'>{$web_scanner}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/filern.php'>{$mass_frn}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/tpl_editor.php'>{$tpl_editor}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/actions.php?act=sphp'>{$config_editor}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/actions.php?act=rtxt'>{$robots_editor}</a></div>
    <div class="list-group-item">{$mark} <a href='{$url}/admincp/actions.php?act=smap'>{$sitemap_editor}</a></div>
</div>