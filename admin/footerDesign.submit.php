<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","vtype,scriptCode,sourceCode");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_design_footer SET
		scriptCode='$scriptCode',sourceCode='$sourceCode'
		WHERE vtype='$vtype'
	");
	
	/*
	완료 후 리턴
	*/
	echo '<!--success::1-->';
?>