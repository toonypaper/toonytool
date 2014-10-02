<?php
	include_once "../include/pageJustice.inc.php";
	
	$tpl = new skinController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/footer.inc.html");
	
	/*
	템플릿 치환
	*/
	echo $tpl->skin_echo();
?>