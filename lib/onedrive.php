<?php
	class onedrive{
		static $client_id;
		static $client_secret;
		static $redirect_uri;
		static $api_url = 'https://graph.microsoft.com/v1.0';
		static $oauth_url = 'https://login.microsoftonline.com/common/oauth2/v2.0';

		//验证URL，浏览器访问、授权
		static function authorize_url(){
			$client_id = self::$client_id;
			$scope = urlencode("offline_access files.readwrite.all");
			$redirect_uri = self::$redirect_uri;
			$url = self::$oauth_url."/authorize?client_id={$client_id}&scope={$scope}&response_type=code&redirect_uri={$redirect_uri}";
			
			if($_SERVER['HTTP_HOST'] != 'localhost'){
				$url .= '&state='.urlencode('http://'.$_SERVER['HTTP_HOST'].get_absolute_path(dirname($_SERVER['PHP_SELF'])));
			}
			
			return $url;
		}

		//使用 $code, 获取 $refresh_token
		static function authorize($code = ""){
			$client_id = self::$client_id;
			$client_secret = self::$client_secret;
			$redirect_uri = self::$redirect_uri;

			$url = self::$oauth_url."/token";
			$post_data = "client_id={$client_id}&redirect_uri={$redirect_uri}&client_secret={$client_secret}&code={$code}&grant_type=authorization_code";
			fetch::$headers = "Content-Type: application/x-www-form-urlencoded";
			$resp = fetch::post($url, $post_data);
			$data = json_decode($resp->content, true);
			return $data;
		}

		//使用 $refresh_token，获取 $access_token
		static function get_token($refresh_token){
			$client_id = self::$client_id;
			$client_secret = self::$client_secret;
			$redirect_uri = self::$redirect_uri;

			$request['url'] = self::$oauth_url."/token";
			$request['post_data']  = "client_id={$client_id}&redirect_uri={$redirect_uri}&client_secret={$client_secret}&refresh_token={$refresh_token}&grant_type=refresh_token";
			$request['headers']= "Content-Type: application/x-www-form-urlencoded";
			$resp = fetch::post($request);
			$data = json_decode($resp->content, true);
			return $data;
		}

		//获取 $access_token, 带缓存
		static function access_token(){
			$token = config('@token');
			if($token['expires_on'] > time()+600){
				return $token['access_token'];
			}else{
				$refresh_token = config('refresh_token');
				$token = self::get_token($refresh_token);
				if(!empty($token['refresh_token'])){
					$token['expires_on'] = time()+ $token['expires_in'];
					config('@token', $token);
					config('refresh_token', $token['refresh_token']);
					return $token['access_token'];
				}
			}
			return "";
		}


		// 生成一个request，带token
		static function request($path="/", $query=""){
			$path = self::urlencode($path);
			$path = empty($path)?'/':":/{$path}:/";
			$token = self::access_token();
			$api_url =  config('@base')['api_url'];
			$request['headers'] = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
			$request['url'] = $api_url."/drive/root".$path.$query;
			return $request;
		}

		
		//返回目录信息
		static function dir($path="/"){
			$request = self::request($path, "children?select=name,size,folder,@microsoft.graph.downloadUrl,lastModifiedDateTime");
			$items = array();
			self::dir_next_page($request, $items);
			//不在列表显示的文件夹
			$hide_list = explode(PHP_EOL,config('onedrive_hide'));
			if(is_array($hide_list) && count($hide_list)>0){
				foreach($hide_list as $hide_dir){
					foreach($items as $key=>$_array){
						if(!empty(trim($hide_dir)) && stristr($key,trim($hide_dir)))unset($items[$key]);
					}
				}
			}
			return $items;
		}

		//通过分页获取页面所有item
		static function dir_next_page($request, &$items, $retry=0){
			$resp = fetch::get($request);
			
			$data = json_decode($resp->content, true);
			if(empty($data) && $retry < 3){
				$retry += 1;
				return self::dir_next_page($request, $items, $retry);
			}
			
			foreach((array)$data['value'] as $item){
				//var_dump($item);
				$items[$item['name']] = array(
					'name'=>$item['name'],
					'size'=>$item['size'],
					'lastModifiedDateTime'=>strtotime($item['lastModifiedDateTime']),
					'downloadUrl'=>$item['@microsoft.graph.downloadUrl'],
					'folder'=>empty($item['folder'])?false:true
				);
			}

			if(!empty($data['@odata.nextLink'])){
				$request = self::request();
				$request['url'] = $data['@odata.nextLink'];
				return self::dir_next_page($request, $items);
			}
		}

		
		//static function content($path){
		//	$token = self::access_token();
		//	fetch::$headers = "Authorization: bearer {$token}";
		//	$url = self::$api_url."/me/drive/root:".self::urlencode($path).":/content";
		//	$resp = fetch::get($url);
		//	return $resp->content;
		//}

		//文件缩略图链接
		static function thumbnail($path,$size='large'){
			$request = self::request($path,"thumbnails/0?select={$size}");
			$resp = fetch::get($request);
			$data = json_decode($resp->content, true);
			$request = self::request($path,"thumbnails/0?select={$size}");
			return @$data[$size]['url'];
		}

		static function upload_large_file($localfile, $remotepath){
			fetch::init([CURLOPT_TIMEOUT=>200]);
			$upload = config('@upload');
			$info = $upload[$remotepath];
			if(empty($info['url'])){
				print ' 创建上传会话'.PHP_EOL;
				$data = self::create_upload_session($remotepath);
				if(!empty($data['uploadUrl'])){
					$info['url'] = $data['uploadUrl'];
					$info['localfile'] = $localfile;
					$info['remotepath'] = $remotepath;
					$info['filesize'] = onedrive::_filesize($localfile);
					$info['offset'] = 0;
					$info['length'] = 327680;
					$info['update_time'] = time();
					$upload[$remotepath] = $info;
					config('@upload', $upload);
				}elseif ( $data === false ){
					print ' 文件已存在!'.PHP_EOL;
					return;
				}
			}
			
			if(empty($info['url'])){
				print ' 获取会话失败！'.PHP_EOL;
				sleep(3);
				return self::upload_large_file($localfile, $remotepath);
			}
			
			print ' 上传分块'.self::human_filesize($info['length']).'	';
			$begin_time = microtime(true);
			$data = self::upload_session($info['url'], $info['localfile'], $info['offset'], $info['length']);

			if(!empty($data['nextExpectedRanges'])){
				$upload_time = microtime(true) - $begin_time;
				$info['speed'] = $info['length']/$upload_time;
				print self::human_filesize($info['speed']).'/s'.'	'.round(($info['offset']/$info['filesize'])*100).'%	'.PHP_EOL;
				$info['length'] = intval($info['length']/$upload_time/32768*2)*327680;
				$info['length'] = ($info['length']>104857600)?104857600:$info['length'];
				
				list($offset, $filesize) = explode('-',$data['nextExpectedRanges'][0]);
				$info['offset'] = $offset;
				$info['update_time'] = time();
				$upload[$remotepath] = $info;
				config('@upload', $upload);
			}elseif(!empty($data['@content.downloadUrl']) || !empty($data['id'])){
				unset($upload[$remotepath]);
				config('@upload', $upload);
				print ' 上传完成！'.PHP_EOL;
				return;
			}else{
				print ' 失败!'.PHP_EOL;
				$data = self::upload_session_status($info['url']);
				if(empty($data)|| $info['length']<100){
					self::delete_upload_session($info['url']);
					unset($upload[$remotepath]);
					config('@upload', $upload);
				}elseif(!empty($data['nextExpectedRanges'])){
					list($offset, $filesize) = explode('-',$data['nextExpectedRanges'][0]);
					$info['offset'] = $offset;
					$info['length'] = $info['length']/1.5;
					$upload[$remotepath] = $info;
					config('@upload', $upload);
				}
			}

			return self::upload_large_file($localfile, $remotepath);
			
		}	
		
		//文件上传函数
		static function upload($path,$content){
			$request = self::request($path,"content");
			$request['post_data'] = $content;
			$resp = fetch::put($request);
			$data = @json_decode($resp->content, true);
			return $data;
		}

		static function upload_url($path, $url){
			$request = self::request(get_absolute_path(dirname($path)),"children");
			$request['headers'] .= "Prefer: respond-async".PHP_EOL;
			$post_data['@microsoft.graph.sourceUrl'] = $url;
			$post_data['name'] = pathinfo($path, PATHINFO_BASENAME );
			$post_data['file'] = json_decode("{}");
			$request['post_data'] = json_encode($post_data);
			$resp = fetch::post($request);
			list($tmp, $location) = explode('ocation:', $resp->headers);
			list($location, $tmp) = explode(PHP_EOL, $location);
			return trim($location);
		}
		
		static function create_upload_session($path){
			$request = self::request($path, 'createUploadSession');
			$request['post_data'] = '{"item": {"@microsoft.graph.conflictBehavior": "fail"}}';
			$token = self::access_token();
			$resp = fetch::post($request);
			$data = json_decode($resp->content, true);
			if($resp->http_code == 409){
				return false;
			}
			return $data;
		}

		static function upload_session($url, $file, $offset, $length=10240){
			$token = self::access_token();
			$file_size = self::_filesize($file);
			$content_length = (($offset+$length)>$file_size)?($file_size-$offset):$length;
			$end = $offset+$content_length-1;
			$post_data = self::file_content($file, $offset, $length);

			$request['url'] = $url;
			$request['curl_opt']=[CURLOPT_TIMEOUT=>360];
			$request['headers'] = "Authorization: bearer {$token}".PHP_EOL;
			$request['headers'] .= "Content-Length: {$content_length}".PHP_EOL;
			$request['headers'] .= "Content-Range: bytes {$offset}-{$end}/{$file_size}";
			$request['post_data'] = $post_data;
			$resp = fetch::put($request);
			$data = json_decode($resp->content, true);
			return $data;
		}

		static function upload_session_status($url){
			$token = self::access_token();
			fetch::$headers = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
			$resp = fetch::get($url);
			$data = json_decode($resp->content, true);
			return $data;
		}

		static function delete_upload_session($url){
			$token = self::access_token();
			fetch::$headers = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
			$resp = fetch::delete($url);
			$data = json_decode($resp->content, true);
			return $data;
		}

		static function file_content($file, $offset, $length){
			$handler = fopen($file, "rb") OR die('获取文件内容失败');
			fseek($handler, $offset);
			
			return fread($handler, $length);
		}

		static function human_filesize($size, $precision = 1) {
			for($i = 0; ($size / 1024) > 1; $i++, $size /= 1024) {}
			return round($size, $precision).(['B','KB','MB','GB','TB','PB','EB','ZB','YB'][$i]);
		}

		static function urlencode($path){
			foreach(explode('/', $path) as $k=>$v){
				if(empty(!$v)){
					$paths[] = rawurlencode($v);
				}
			}
			return @join('/',$paths);
		}
			
		static function _filesize($path){
		    if (!file_exists($path))
		        return false;
		    $size = filesize($path);
		    
		    if (!($file = fopen($path, 'rb')))
		        return false;
		    
		    if ($size >= 0){//Check if it really is a small file (< 2 GB)
		        if (fseek($file, 0, SEEK_END) === 0){//It really is a small file
		            fclose($file);
		            return $size;
		        }
		    }
		    
		    //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
		    $size = PHP_INT_MAX - 1;
		    if (fseek($file, PHP_INT_MAX - 1) !== 0){
		        fclose($file);
		        return false;
		    }
		    
		    $length = 1024 * 1024;
		    while (!feof($file)){//Read the file until end
		        $read = fread($file, $length);
		        $size = bcadd($size, $length);
		    }
		    $size = bcsub($size, $length);
		    $size = bcadd($size, strlen($read));
		    
		    fclose($file);
		    return $size;
		}
	}
