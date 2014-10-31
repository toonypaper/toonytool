<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","sourceCode");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	$validator->validt_tags("sourceCode",1,"");
	
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_mailling_template SET
		source='$sourceCode',regdate=now()
		WHERE type='account'
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=mailTplAccount");
?>