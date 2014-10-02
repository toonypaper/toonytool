<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}main.html");

	/*
	템플릿 치환
	*/
	echo $tpl->skin_echo();
?>