<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$fileUploader = new fileUploader();
	$method = new methodController();
	
	$method->method_param("POST","file");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	요청 받은 스마트에디터 파일을 삭제 처리
	*/
	$fileUploader->savePath = __DIR_PATH__."upload/smartEditor/";
	$fileUploader->fileDelete($file);
?>