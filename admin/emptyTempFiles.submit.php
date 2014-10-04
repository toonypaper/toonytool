<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	//삭제할 폴더의 유효성 검사
	$sessionCookiesDir = __DIR_PATH__."upload/sessionCookies/";
	if(!is_dir($sessionCookiesDir)){
		echo '<!--error::not_sessionCookiesPath-->';
		exit;
	}
	
	/*
	임시파일 삭제
	*/
	$open_dir = opendir($sessionCookiesDir);
	$del_num = 0;
	while(($read=readdir($open_dir))!=false){
		$fh = fopen($sessionCookiesDir.$read, 'r'); 
		while (!feof($fh)){ 
			 $vContent = fread($fh,2098); 
		} 
		fclose($fh); 
		//Delete
		if(0<strlen($vContent)){
			if(unlink($sessionCookiesDir.$read)){
				$del_num++;
			}
		}
	}
	
	/*
	완료 후 리턴
	*/
	echo "<!--success::1-->{$del_num}";
	
	
?>