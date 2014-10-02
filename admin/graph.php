<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$paging = new pagingClass();
	$method = new methodController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/graph.html");

	/*
	템플릿 치환
	*/
	echo $tpl->skin_echo();
?>