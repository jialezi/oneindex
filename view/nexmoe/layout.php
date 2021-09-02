<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
	<title><?php e($title.' - '.config('site_name'));?></title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.2/dist/jquery.fancybox.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.css">
	<link rel="shortcut icon" href="https://image.suning.cn/uimg/ZR/share_order/158562104413864293.jpg">
    <link rel="stylesheet" href="view/nexmoe/app.css">
</head>
<body class=" mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink">
	<header class="mdui-appbar mdui-appbar-fixed mdui-color-theme mdui-appbar-inset">	
	<div class="mdui-toolbar mdui-color-theme">
        <span class="mdui-btn  mdui-typo-headline mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}" mdui-tooltip="{content: '菜单'}"><i class="mdui-icon material-icons">menu</i></span>
		<a href="/" class="mdui-typo-headline"><?php e(config('site_name'));?></a>
		<?php foreach((array)$navs as $n=>$l):?>
			<i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
			<a href="<?php e($l);?>"><?php e($n);?></a>
		<?php endforeach;?>
		<div class="mdui-toolbar-spacer"></div>
		<a href="javascript:thumb();" id="thumb" class="mdui-btn mdui-btn-icon" mdui-tooltip="{content: '切换显示'}"><i class="mdui-icon material-icons">format_list_bulleted</i></a>
	</div>	
	</header>

<div class="mdui-drawer mdui-drawer-close mdui-color-indigo-50" id="main-drawer">
	<div class="mdui-grid-tile">
		<a href="javascript:;"><img src="<?php e(config('drawer_img'));?>"/></a>
		<div class="mdui-grid-tile-actions mdui-grid-tile-actions-gradient">
			<div class="mdui-grid-tile-text">
				<div class="mdui-grid-tile-title"><?php e($title.' - '.config('site_name'));?></div>
				<div class="mdui-grid-tile-subtitle">Onedrive</div>
    			</div>
   		 </div>
	</div>   
	<div class="mdui-list" mdui-collapse="{accordion: true}">
    		<?php e(config('drawer'));?>
	</div>
</div>
<div class="mdui-container">
	<div class="mdui-container-fluid"></div>
    	<?php view::section('content');?>
</div>
<script src="https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/sentsin/layer@3.5.0/dist/layer.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.2/dist/jquery.fancybox.min.js"></script>
<script src="view/nexmoe/app.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(".fancybox").fancybox();
    });
</script>
</body>
</html>
