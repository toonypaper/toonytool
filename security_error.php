<?php
	include_once "include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$lib = new libraryClass();
	
	/*
	템플릿 로드
	*/
	$tpl = new skinController();
	$tpl->skin_file_path("_tpl/security_error.html");
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[why]",htmlspecialchars($this->fetch("memo")));
	$tpl->skin_modeling("[when]",date("Y년 m월 d일 H:i",strtotime($this->fetch("regdate"))));
	$tpl->skin_modeling("[email]","<a href=\"mailto:{$site_config['ad_email']}\">".$site_config['ad_email']."</a>");
	$tpl->skin_modeling("[file_dir]",__URL_PATH__);
	
	echo $tpl->skin_echo();
?>