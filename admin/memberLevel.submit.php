<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","level_1,level_2,level_3,level_4,level_5,level_6,level_7,level_8,level_9");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	레벨 POST변수 배열화
	*/
	$level['1'] = $level_1;
	$level['2'] = $level_2;
	$level['3'] = $level_3;
	$level['4'] = $level_4;
	$level['5'] = $level_5;
	$level['6'] = $level_6;
	$level['7'] = $level_7;
	$level['8'] = $level_8;
	$level['9'] = $level_9;
	
	/*
	검사
	*/
	for($i=1;$i<=9;$i++){
		if(trim($level[$i])==""){
			echo '<!--error::null_levelName-->';
			exit;
		}
	}
	
	/*
	DB 수정
	*/
	$level_vars = implode(",",$level);
	$mysql->query("
		UPDATE toony_admin_siteconfig
		SET ad_member_type='$level_vars'
	");
	
	
	/*
	완료 후 리턴
	*/
	echo '<!--success::1-->';
	
	
?>