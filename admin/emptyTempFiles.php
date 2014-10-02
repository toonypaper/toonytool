<?php
	$tpl = new skinController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/emptyTempFiles.html");

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[sessionCookiePath]",__DIR_PATH__."upload/sessionCookies/");
	
	echo $tpl->skin_echo();
?>