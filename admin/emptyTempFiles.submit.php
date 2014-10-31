<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	$fileUploader = new fileUploader();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	//삭제할 폴더의 유효성 검사
	$fileUploader->savePath = __DIR_PATH__."upload/sessionCookies/";
	$fileUploader->filePathCheck(); //이렉터리가 존재하지 않는다면 생성
	
	/*
	임시파일 삭제
	*/
	$open_dir = opendir($fileUploader->savePath);
	$del_num = 0;
	while(($read=readdir($open_dir))!=false){
		$fh = fopen($fileUploader->savePath.$read, 'r'); 
		echo $read;
		while (!feof($fh)){ 
			 $vContent = fread($fh,2098); 
		} 
		fclose($fh); 
		//Delete
		if(0<strlen($vContent)){
			$fileUploader->fileDelete($read);
		}
		$del_num++;
	}
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success($del_num."개의 임시파일이 성공적으로 삭제 되었습니다.","admin/?p=emptyTempFiles");
	
	
?>