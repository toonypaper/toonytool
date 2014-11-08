<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","idno,zindex,vtype");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	DB 수정
	*/
	for($i=0;$i<count($idno);$i++){
		$mysql->query("
			UPDATE toony_admin_menuInfo
			SET zindex='$zindex[$i]'
			WHERE idno='$idno[$i]'
		");
	}
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=menuSetting&vtype={$vtype}");
?>