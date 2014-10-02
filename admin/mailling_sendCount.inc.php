<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","min_level,max_level");
	
	/*
	검사
	*/
	if($min_level=="none"){
		echo '<!--error::null_min_level-->'; exit;
	}
	if($max_level=="none"){
		echo '<!--error::null_max_level-->'; exit;
	}
	
	/*
	발송 대상 인원수 구함
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE (me_level<=$min_level AND me_level>=$max_level) AND me_drop_regdate IS NULL
		ORDER BY me_regdate DESC
	");
	
	/*
	완료 후 리턴
	*/
	echo $mysql->numRows();
?>