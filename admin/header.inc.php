<?php
	include_once "../include/pageJustice.inc.php";
	
	$tpl = new skinController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/header.inc.html");
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[gotoHomepageUrl]",$site_config['ad_site_url']);
	
	echo $tpl->skin_echo();
?>