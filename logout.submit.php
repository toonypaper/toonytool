<?php
	include_once "include/engine.inc.php";
	include_once __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$session = new sessionController();
	$mysql = new mysqlConnection();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_post");
	
	/*
	검사
	*/
	if(isset($__toony_member_idno)==false){
		$lib->error_alert_location("로그인 되어 있지 않습니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	현재 접속자 정보 삭제
	*/
	$mysql->select("
		SELECT me_idno
		FROM toony_admin_member_online
		WHERE me_idno='{$member['me_idno']}'
	");
	if($mysql->numRows()>0){
		$mysql->query("
			DELETE FROM toony_admin_member_online
			WHERE me_idno='{$member['me_idno']}'
		");
	}
	
	/*
	로그인 세션 삭제
	*/
	$session->session_deleter("__toony_member_idno");
	
	
	/*
	완료 후 페이지 이동
	*/
	//리페러 체크하여 PC모드인지 Mobile모드인지 확인
	$referer = $_SERVER['HTTP_REFERER'];
	if(strstr($referer,"/m/")==true){
		$callbackUri = $site_config['ad_msite_url'];
	}else{
		$callbackUri = $site_config['ad_site_url'];
	}
	//로그아웃 후 이동할 페이지
	$lib->error_location($callbackUri,"A");
	
?>