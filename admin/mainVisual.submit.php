<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","scriptCode,sourceCode,vtype");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_design_mainVisual SET
		scriptCode='$scriptCode',sourceCode='$sourceCode'
		WHERE vtype='$vtype'
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=mainVisual");
?>