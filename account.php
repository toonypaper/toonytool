<?php
	include_once "include/pageJustice.inc.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$tpl = new skinController();
	
	/*
	검사
	*/
	if($member[me_level]<10){
		$lib->error_alert_location("이미 가입 되어 있습니다.",__URL_PATH__.$viewDir,"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}account.html");
	
	/*
	템플릿 치환
	*/
	echo $tpl->skin_echo();
?>