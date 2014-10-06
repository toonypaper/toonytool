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
	//본문에 사용금지 태그가 있는지 검사
	$lib->not_tags_check($sourceCode,"<!--error::have_not_tags-->");
	
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
	echo '<!--success::1-->';
?>