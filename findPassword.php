<?php
	include_once "include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$lib = new libraryClass();
	
	/*
	검사
	*/
	if($member['me_level']<10){
		$lib->error_alert_location("이미 로그인 되어 있습니다.",__URL_PATH__.$viewDir,"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}findPassword.html");
	
	/*
	템플릿 치환
	*/
	
	echo $tpl->skin_echo();
?>