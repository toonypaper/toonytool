<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","sourceCode");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if(trim($sourceCode)==""){
		echo '<!--error::null_sourceCode-->'; exit;
	}
	
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_mailling_template SET
		source='$sourceCode',regdate=now()
		WHERE type='mailling'
	");
	
	/*
	완료 후 리턴
	*/
	echo '<!--success::1-->';
?>