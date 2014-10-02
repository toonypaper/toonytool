<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	기본 정보 로드
	*/
	$mysql->select("
		SELECT source
		FROM toony_admin_mailling_template
		WHERE type='mailling'
	");
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$sourceCode = $mysql->fetch("source");
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/mailTplMailling.html");

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[sourceCode]",$sourceCode);
	
	echo $tpl->skin_echo();
?>