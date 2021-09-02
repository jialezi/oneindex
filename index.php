<?php

require  __DIR__.'/init.php';

//国际版/世纪互联 api写进配置文件
onedrive::$oauth_url = config("oauth_url");
onedrive::$api_url = config("api_url");


/**
 *    程序安装
 */
if( empty( config('refresh_token') ) ){
	route::any('/','AdminController@install');
}

/**
 *    系统后台
 */
route::group(function(){
	return ($_COOKIE['admin'] == md5(config('password').config('refresh_token')) );
},function(){
	route::get('/logout','AdminController@logout');
	route::any('/admin/','AdminController@settings');
	route::any('/admin/cache','AdminController@cache');
	route::any('/admin/show','AdminController@show');
	route::any('/admin/setpass','AdminController@setpass');
	route::any('/admin/images','AdminController@images');
	route::any('/admin/account','AdminController@account');
	route::any('/admin/upload','UploadController@index');
	//守护进程
	route::any('/admin/upload/run','UploadController@run');
	//上传进程
	route::post('/admin/upload/task','UploadController@task');
});
//登陆
route::any('/login','AdminController@login');

//跳转到登陆
route::any('/admin/',function(){
	return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/login');
});



define('VIEW_PATH', ROOT.'view/'.(config('style')?config('style'):'material').'/');
/**
 *    OneImg
 */
$images = config('images@base');
if( ($_COOKIE['admin'] == md5(config('password').config('refresh_token')) || $images['public']) ){
	route::any('/upload','ImagesController@upload');
	route::any('/images','ImagesController@index');
	if($images['home']){
		route::any('/','ImagesController@index');
	}
}
/**
 *    列目录
 */
route::any('{path:#all}','IndexController@index');
