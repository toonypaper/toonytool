<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","idno,zindex");
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
	echo '<!--success::1-->';
?>