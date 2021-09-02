<?php view::layout('layout')?>
<?php
$downloadUrl = $item['downloadUrl'];
 	if (config('proxy_domain') != ""){
 	$downloadUrl = str_replace(config('main_domain'),config('proxy_domain'),$item['downloadUrl']);
 	}else {
 		$downloadUrl = $item['downloadUrl'];
 	}
?>
<?php view::begin('content');?>
<div class="mdui-container-fluid">
    <div class="nexmoe-item">
	<img class="mdui-img-fluid mdui-center" style="max-height:700px;margin:10px auto;" src="<?php e($downloadUrl);?>"/>
	<div class="mdui-textfield">
	  <label class="mdui-textfield-label">下载地址</label>
	  <input class="mdui-textfield-input" type="text" value="<?php e($url);?>"/>
	</div>
	<div class="mdui-textfield">
	  <label class="mdui-textfield-label">HTML 引用地址</label>
	  <input class="mdui-textfield-input" type="text" value="<img src='<?php e($url);?>' />"/>
	</div>
        <div class="mdui-textfield">
	  <label class="mdui-textfield-label">Markdown 引用地址</label>
	  <input class="mdui-textfield-input" type="text" value="![](<?php e($url);?>)"/>
	</div>
        <br>
    </div>
</div>
<a href="<?php e($url);?>" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">file_download</i></a>
<?php view::end('content');?>
