<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, shrink-to-fit=no, viewport-fit=cover">
	<title>OneImages图床。</title>
	<meta name="keywords" content="OneImages图床,OneImages,oneindex,onedrive" />
	<link rel="shortcut icon" href="https://pic.rmb.bdstatic.com/bjh/7f258369bc7cae227053a6588adb2453.png" />
  	<link rel="stylesheet" href="/view/nexmoe/images/static/style.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
	<script src="/view/nexmoe/images/static/file.js"></script>
	<script>var _hmt=_hmt||[];(function(){var hm=document.createElement("script");hm.src="https://hm.baidu.com/hm.js?47096df5fe81654d11927a045faab501";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm,s)})();</script>
	<meta name="referrer" content="no-referrer" />
</head>
<body>
	<input id="file" type="file" multiple="multiple" style="display: none;">
	<div class="container">
		<div class="upload">
			<div class="title">OneImages图床<small style="font-size:10px;">(最大支持100M)</small><input id="xkx" autocomplete="off" maxlength="16" style="display: none;" placeholder="请上传...">
			</div>
			<div class="content" id="dragbox">
				<svg class="icon" viewBox="0 0 1335 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
					<path d="M1097.060174 392.125217C1073.730783 172.966957 893.261913 0.378435 666.089739 0.378435c-227.127652 0-415.610435 171.920696-430.948174 391.746782C101.910261 415.454609 0 525.356522 0 666.601739c0 149.147826 125.239652 274.476522 274.476522 274.476522h195.828869v-78.669913H274.476522a193.691826 193.691826 0 0 1-195.940174-195.806609c0-102.021565 70.678261-180.580174 172.588522-195.917913l54.561391-8.013913 8.013913-62.553043c16.005565-180.580174 172.588522-321.157565 352.389565-321.157566 180.580174 0 337.029565 141.356522 352.389565 321.157566v62.553043l62.664348 8.013913c101.910261 16.005565 172.477217 93.896348 172.477218 195.917913 0 109.901913-85.904696 195.806609-195.806609 195.806609h-195.917913v78.580869h196.029217c149.147826 0 274.476522-125.261913 274.476522-274.476521 0-141.133913-101.999304-259.072-235.25287-274.387479" p-id="2345" fill="#909399"></path>
					<path d="M612.218435 364.766609l1.335652 2.003478L389.698783 590.58087l55.229217 55.362782 181.938087-181.938087V1018.88h78.558609v-78.58087h156.471652-156.471652V458.039652l183.808 183.919305 55.340521-55.340522-277.147826-277.058783-55.229217 55.229218z m-141.913044 575.666087h156.471652-156.716521 0.222608z" p-id="2346" fill="#909399"></path>
				</svg>
				<p class="desc">点击上传 / 粘贴上传 / 拖拽上传</p>
				<p class="desc">将文件存储到OneDrive云盘</p>
			</div>
		</div>
		<div class="filelist">
			<div class="title">上传列表
			    <div class="copyall" style="display:none">
					<button onclick="sel(this);" name="xkx" id="_url">URL</button>
					<button onclick="sel(this);" name="xkx" id="_html">HTML</button>
					<button onclick="sel(this);" name="xkx" id="_Ubb">UBB</button>
					<button onclick="sel(this);" name="xkx" id="_markdown">MD</button>
					<button onclick="copyAll(this);" name="xkx" id="copyAll" style="width:70px;background-color: #d0dbee;">复制全部</button>
                </div>
            </div>
			<div class="list"></div>
		</div>
	</div>
	<div id="footer" style="position:fixed;width: 100%;text-align: center;bottom: 0px;display: block;">
		<div style="height: 20px">
		<p style="color:#000;">您的IP:
		<script src="https://pv.sohu.com/cityjson?ie=utf-8"></script>
		<script>document.write(returnCitySN["cname"])</script>  
		© 
		<a style="text-decoration:none;" href="https://api.xkx.me/">更多：xKx.Me</a>
		</p>
		</div>
		</div>
</body>
</html>
