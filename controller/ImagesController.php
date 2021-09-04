<?php 

class ImagesController{
	function generateRandomString($length) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }
	
	function upload(){
		if($this->is_image($_FILES["file"]) ){
			$content =  $_FILES["file"]['tmp_name'];
			$remotepath =  'images/'.date('Y/m/d/').$this->generateRandomString(10).'/';
			$remotefile = $remotepath.$_FILES["file"]['name'];
			ob_start(); 
			$result = onedrive::upload_large_file($content,config('onedrive_root').$remotefile);
			ob_end_clean(); 
			//if($result){
				$root = get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).config('root_path');
				$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
				$pre = $_SERVER['HTTP_HOST'].$root.'/'.$remotefile.((config('root_path') == '?')?'&s':'?s');
				$pre = $http_type.str_replace('//','/', $pre);
				$url = $_SERVER['HTTP_HOST'].$root.'/'.$remotefile;
				$url = $http_type.str_replace('//','/', $url);

				//view::direct($url);
				view::json($_FILES["file"]['name'],$_FILES["file"]['size'],$url,$pre);
			//}
		}
		return view::direct('/images');
		//return view::load('images/index');
	}
    
    function index(){
		return view::load('images/index');
	}

	function is_image($file){
        if(!$file){
            return false;
        }  
		$config = config('images@base');
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(!in_array($ext,$config['exts'])){
			return false;
		}
		if($file['size'] > 10485760 || $file['size'] == 0){
			return false;
		}

		return true;
	}
}
