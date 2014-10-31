<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	레이아웃 스킨 템플릿 로드
	*/
	$tpl->skin_file_path("layoutskin/".CALLED_LAYOUTDIR."main.html");

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[/layoutskinDir/]",__URL_PATH__."layoutskin/".CALLED_LAYOUTDIR);
	echo $tpl->skin_echo();
?>